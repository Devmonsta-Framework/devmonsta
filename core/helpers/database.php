<?php

if ( !defined( 'DEVM' ) ) {
    die( 'Forbidden' );
}

// Post Options
class DEVM_Db_Options_Model_Post extends DEVM_Db_Options_Model {

    /**
     * returns post meta data
     *
     * @param String $post_id
     * @param String $option_id
     * @param String $default_value
     * @return void
     */
    function option( $post_id, $option_id, $default_value = null ) {

        $prefix    = 'devmonsta_';
        $option_id = $prefix . $option_id;
        $post_id   = intval( $post_id );
        return get_post_meta( $post_id, $option_id, true );
    }

    protected function get_id() {
        return 'post';
    }

    private function get_cache_key( $key, $item_id = null, array $extra_data = [] ) {
        return 'devm-options-model:' . $this->get_id() . '/' . $key;
    }

    private function get_post_id( $post_id ) {
        $post_id = intval( $post_id );

        try {
            // Prevent too often execution of wp_get_post_autosave() because it does WP Query
            return DEVM_Cache::get( $cache_key = $this->get_cache_key( 'id/' . $post_id ) );
        } catch ( DEVM_Cache_Not_Found_Exception $e ) {

            if ( !$post_id ) {
                /** @var WP_Post $post */
                global $post;

                if ( !$post ) {
                    return null;
                } else {
                    $post_id = $post->ID;
                }

                /**
                 * Check if is Preview and use the preview post_id instead of real/current post id
                 *
                 * Note: WordPress changes the global $post content on preview:
                 * 1. https://github.com/WordPress/WordPress/blob/2096b451c704715db3c4faf699a1184260deade9/wp-includes/query.php#L3573-L3583
                 * 2. https://github.com/WordPress/WordPress/blob/4a31dd6fe8b774d56f901a29e72dcf9523e9ce85/wp-includes/revision.php#L485-L528
                 */

                if ( is_preview() && is_object( $preview = wp_get_post_autosave( $post->ID ) ) ) {
                    $post_id = $preview->ID;
                }

            }

            DEVM_Cache::set( $cache_key, $post_id );

            return $post_id;
        }

    }

    private function get_post_type( $post_id ) {
        $post_id = $this->get_post_id( $post_id );

        try {
            return DEVM_Cache::get( $cache_key = $this->get_cache_key( 'type/' . $post_id ) );
        } catch ( DEVM_Cache_Not_Found_Exception $e ) {
            DEVM_Cache::set(
                $cache_key,
                $post_type = get_post_type(
                    ( $post_revision_id = wp_is_post_revision( $post_id ) ) ? $post_revision_id : $post_id
                )
            );

            return $post_type;
        }

    }

    protected function get_options( $item_id, array $extra_data = [] ) {
        $post_type = $this->get_post_type( $item_id );

        if ( apply_filters( 'devm_get_db_post_option:devm-storage-enabled',
            $post_type !== 'devm-slider',
            $post_type
        ) ) {
            return devm()->theme->get_post_options( $post_type );
        } else {
            return [];
        }

    }

    protected function get_values( $item_id, array $extra_data = [] ) {
        return DEVM_WP_Meta::get( 'post', $this->get_post_id( $item_id ), 'devm_options', [] );
    }

    protected function set_values( $item_id, $values, array $extra_data = [] ) {
        DEVM_WP_Meta::set( 'post', $this->get_post_id( $item_id ), 'devm_options', $values );
    }

    protected function get_devm_storage_params( $item_id, array $extra_data = [] ) {
        return ['post-id' => $this->get_post_id( $item_id )];
    }

    protected function get_cache_key_protected( $key, $item_id, array $extra_data = [] ) {

        if ( $key === 'options' ) {
            // Cache options grouped by post-type, not by post id
            return ( $post_type = $this->get_post_type( $item_id ) ) ? $post_type : '?';
        } else {
            return $this->get_post_id( $item_id );
        }

    }

    protected function after_set_protected( $post_id, $option_id, $sub_keys, $old_value, array $extra_data = [] ) {
        /**
         * @deprecated
         */
        devm()->backend->_sync_post_separate_meta( $post_id );

        /**
         * @since 2.2.8
         */
        do_action( 'devm_post_options_update',
            $post_id,
            /**
             * Option id
             * First level multi-key
             * For e.g. if $option_id is 'hello/world/7' this will be 'hello'
             */
            $option_id,
            /**
             * The remaining sub-keys
             * For e.g.
             * if $option_id is 'hello/world/7' this will be array('world', '7')
             * if $option_id is 'hello' this will be array()
             */
            explode( '/', $sub_keys ),
            /**
             * Old post option(s) value
             * @since 2.3.3
             */
            $old_value
        );
    }

    /**
     * Set and get post option value
     *
     * @return void
     */
    protected function _init() {

        function devm_post_option( $post_id = null, $option_id = null, $default_value = null ) {
            return DEVM_Db_Options_Model::get_instance_protected( 'post' )->get( intval( $post_id ), $option_id, $default_value );
        }

        /**
         * Set post option value in database
         *
         * @param null|int $post_id
         * @param string|null $option_id Specific option id (accepts multikey). null - all options
         * @param $value
         */
        function devm_set_post_option( $post_id = null, $option_id = null, $value = '' ) {
            DEVM_Db_Options_Model::get_instance_protected( 'post' )->set( intval( $post_id ), $option_id, $value );
        }

        // todo: add_action() to clear the DEVM_Cache
    }

}

// Term Options
class DEVM_Db_Options_Model_Term extends DEVM_Db_Options_Model {

    protected function get_id() {
        return 'term';
    }

    protected function get_values( $item_id, array $extra_data = [] ) {
        self::migrate( $item_id );

        return (array) get_term_meta( $item_id, 'devm_options', true );
    }

    protected function set_values( $item_id, $values, array $extra_data = [] ) {
        self::migrate( $item_id );

        update_term_meta( $item_id, 'devm_options', $values );
    }

    protected function get_options( $item_id, array $extra_data = [] ) {
        return devm()->theme->get_taxonomy_options( $extra_data['taxonomy'] );
    }

    protected function get_devm_storage_params( $item_id, array $extra_data = [] ) {
        return [
            'term-id'  => $item_id,
            'taxonomy' => $extra_data['taxonomy'],
        ];
    }

    protected function get_cache_key_protected( $key, $item_id, array $extra_data = [] ) {

        if ( $key === 'options' ) {
            return $extra_data['taxonomy']; // Cache options grouped by taxonomy, not by term id
        } else {
            return $item_id;
        }

    }

    /**
     * Cache termmeta table name if exists
     * @var string|false
     */
    private static $old_table_name;

    /**
     * @return string|false
     */
    private static function get_old_table_name() {

        if ( is_null( self::$old_table_name ) ) {
            /** @var WPDB $wpdb */
            global $wpdb;

            $table_name = $wpdb->get_results( "show tables like '{$wpdb->prefix}devm_termmeta'", ARRAY_A );
            $table_name = $table_name ? array_pop( $table_name[0] ) : false;

            if ( $table_name && !$wpdb->get_results( "SELECT 1 FROM `{$table_name}` LIMIT 1" ) ) {
                // The table is empty, delete it
                $wpdb->query( "DROP TABLE `{$table_name}`" );
                $table_name = false;
            }

            self::$old_table_name = $table_name;
        }

        return self::$old_table_name;
    }

    /**
     * @internal
     */
    public static function action_switch_blog_protected() {
        self::$old_table_name = null; // reset
    }

    /**
     * When a term is deleted, delete its meta from old devm_termmeta table
     *
     * @param mixed $term_id
     *
     * @return void
     * @internal
     */
    public static function action_devm_delete_term_protected( $term_id ) {

        if ( !( $table_name = self::get_old_table_name() ) ) {
            return;
        }

        $term_id = (int) $term_id;

        if ( !$term_id ) {
            return;
        }

        /** @var WPDB $wpdb */
        global $wpdb;

        $wpdb->delete( $table_name, ['devm_term_id' => $term_id], ['%d'] );
    }

    /**
     * In WP 4.4 was introduced native term meta https://codex.wordpress.org/Version_4.4#For_Developers
     * All data from old table must be migrated to native term meta
     * @param int $term_id
     * @return bool
     */
    private static function migrate( $term_id ) {
        global $wpdb; /** @var wpdb $wpdb */

        if (
            ( $old_table_name = self::get_old_table_name() )
            &&
            ( $value = $wpdb->get_col( $wpdb->prepare(
                "SELECT meta_value FROM `{$old_table_name}` WHERE devm_term_id = %d AND meta_key = 'devm_options' LIMIT 1",
                $term_id
            ) ) )
            &&
            ( $value = unserialize( $value[0] ) )
        ) {
            $wpdb->delete( $old_table_name, ['devm_term_id' => $term_id], ['%d'] );

            update_term_meta( $term_id, 'devm_options', $value );

            return true;
        } else {
            return false;
        }

    }

    protected function after_set_protected( $item_id, $option_id, $sub_keys, $old_value, array $extra_data = [] ) {
        /**
         * @since 2.6.0
         */
        do_action( 'devm_term_options_update', [
            'term_id'   => $item_id,
            'taxonomy'  => $extra_data['taxonomy'],
            /**
             * Option id
             * First level multi-key
             * For e.g. if $option_id is 'hello/world/7' this will be 'hello'
             */
            'option_id' => $option_id,
            /**
             * The remaining sub-keys
             * For e.g.
             * if $option_id is 'hello/world/7' this will be array('world', '7')
             * if $option_id is 'hello' this will be array()
             */
            'sub_keys'  => explode( '/', $sub_keys ),
            /**
             * Old option(s) value
             */
            'old_value' => $old_value,
        ] );
    }

    /**
     * Set and get term option
     *
     * @return void
     */
    protected function _init() {

        function devm_term_option( $term_id, $taxonomy, $option_id = null, $default_value = null, $get_original_value = null ) {

            if ( !taxonomy_exists( $taxonomy ) ) {
                return null;
            }

            return DEVM_Db_Options_Model::get_instance_protected( 'term' )->get( intval( $term_id ), $option_id, $default_value, [
                'taxonomy' => $taxonomy,
            ] );
        }

        /**
         * Set term option value in database
         *
         * @param int $term_id
         * @param string $taxonomy
         * @param string|null $option_id Specific option id (accepts multikey). null - all options
         * @param mixed $value
         *
         * @return null
         */
        function devm_set_term_option( $term_id, $taxonomy, $option_id = null, $value ='' ) {

            if ( !taxonomy_exists( $taxonomy ) ) {
                return null;
            }

            DEVM_Db_Options_Model::get_instance_protected( 'term' )->set( intval( $term_id ), $option_id, $value, [
                'taxonomy' => $taxonomy,
            ] );
        }

        add_action( 'switch_blog', [__CLASS__, 'action_switch_blog_protected'] );
        add_action( 'delete_term', [__CLASS__, 'action_devm_delete_term_protected'] );
    }

}

new DEVM_Db_Options_Model_Term();

// Customizer Options
class DEVM_Db_Options_Model_Customizer extends DEVM_Db_Options_Model {
    protected function get_id() {
        return 'customizer';
    }

    protected function get_values( $item_id, array $extra_data = [] ) {
        return get_theme_mod( 'devm_options', [] );
    }

    protected function set_values( $item_id, $values, array $extra_data = [] ) {
        set_theme_mod( 'devm_options', $values );
    }

    protected function get_options( $item_id, array $extra_data = [] ) {
        return devm()->theme->get_customizer_options();
    }

    protected function get_devm_storage_params( $item_id, array $extra_data = [] ) {
        return [
            'customizer' => true,
        ];
    }

    protected function after_set_protected( $item_id, $option_id, $sub_keys, $old_value, array $extra_data = [] ) {
        /**
         * @since 2.6.0
         */
        do_action( 'devm_customizer_options_update', [
            /**
             * Option id
             * First level multi-key
             * For e.g. if $option_id is 'hello/world/7' this will be 'hello'
             */
            'option_id' => $option_id,
            /**
             * The remaining sub-keys
             * For e.g.
             * if $option_id is 'hello/world/7' this will be array('world', '7')
             * if $option_id is 'hello' this will be array()
             */
            'sub_keys'  => explode( '/', $sub_keys ),
            /**
             * Old option(s) value
             */
            'old_value' => $old_value,
        ] );
    }

    /**
     * @internal
     */
    public function reset_cache_protected() {
        Devm_Cache::del( $this->get_main_cache_key() );
    }

    protected function _init() {
        /**
         * Get a customizer framework option value from the database
         *
         * @param string|null $option_id Specific option id (accepts multikey). null - all options
         * @param null|mixed $default_value If no option found in the database, this value will be returned
         *
         * @return mixed|null
         */
        function devm_customizer_option( $option_id = null, $default_value = null ) {
            return DEVM_Db_Options_Model::get_instance_protected( 'customizer' )->get( null, $option_id, $default_value );
        }

        /**
         * Set a theme customizer option value in database
         *
         * @param null $option_id Specific option id (accepts multikey). null - all options
         * @param mixed $value
         */
        function devm_set_customizer_option( $option_id = null, $value = '') {
            DEVM_Db_Options_Model::get_instance_protected( 'customizer' )->set( null, $option_id, $value );
        }

        add_action( 'customize_preview_init', [$this, 'reset_cache_protected'],
            1
        );
    }

}

new DEVM_Db_Options_Model_Customizer();
