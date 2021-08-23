<?php

if ( !defined( 'DEVM' ) ) {
    die( 'Forbidden' );
}

/**
 * Lets you create easy functions for get/set database option values
 * it will handle all clever logic with default values, multikeys and processing options devm-storage parameter
 * @since 2.5.9
 */
abstract class DEVM_Db_Options_Model {
    /**
     * @return string Must not contain '/'
     */
    abstract protected function get_id();

    /**
     * @param null|int|string $item_id
     * @param array $extra_data
     * @return mixed
     */
    abstract protected function get_values( $item_id, array $extra_data = [] );

    /**
     * @param null|int|string $item_id
     * @param mixed $values
     * @param array $extra_data
     * @return void
     */
    abstract protected function set_values( $item_id, $values, array $extra_data = [] );

    /**
     * @param null|int|string $item_id
     * @param array $extra_data
     * @return array
     */
    abstract protected function get_options( $item_id, array $extra_data = [] );

    /**
     * @param null|int|string $item_id
     * @param array $extra_data
     * @return array E.g. for post options {'post-id': $item_id}
     * @see devm_db_option_storage_type()
     */
    abstract protected function get_devm_storage_params( $item_id, array $extra_data = [] );

    abstract protected function _init();

    /**
     * @param null|int|string $item_id
     * @param null|string $option_id
     * @param null|string $sub_keys
     * @param mixed $old_value
     * @param array $extra_data
     */
    protected function after_set_protected( $item_id, $option_id, $sub_keys, $old_value, array $extra_data = [] ) {}

    /**
     * Get sub-key. For e.g. if each item must have a separate key or not.
     * @param string $key
     * @param null|int|string $item_id
     * @param array $extra_data
     * @return null|string
     */
    protected function get_cache_key_protected( $key, $item_id, array $extra_data = [] ) {
        return empty( $item_id ) ? null : $item_id;
    }

    /**
     * @var array {'id': mixed}
     */
    private static $instances = [];

    /**
     * @param string $id
     * @return DEVM_Db_Options_Model
     * @internal
     */
    final public static function get_instance_protected( $id ) {
        return self::$instances[$id];
    }

    /**
     * @return string
     * @since 2.6.7
     */
    final public function get_main_cache_key() {
        return 'devm-options-model:' . $this->get_id();
    }

    final public function __construct() {

        if ( isset( self::$instances[$this->get_id()] ) ) {
            trigger_error( __CLASS__ . ' with id "' . $this->get_id() . '" was already defined', E_USER_ERROR );
        } else {
            self::$instances[$this->get_id()] = $this;
        }

        $this->_init();
    }

    private function get_cache_key( $key, $item_id, array $extra_data = [] ) {
        $item_key = $this->get_cache_key_protected( $key, $item_id, $extra_data );

        return $this->get_main_cache_key() . '/' . $key . ( empty( $item_key ) ? '' : '/' . $item_key );
    }

    /**
     * @param null|int|string $item_id
     * @param null|string $option_id
     * @param mixed $default_value
     * @param array $extra_data
     * @return mixed
     */
    final public function get( $item_id = null, $option_id = null, $default_value = null, array $extra_data = [] ) {

        if ( is_preview() ) {
            global $wp_query;

            if ( $wp_query->queried_object && ( is_single( $item_id ) || is_page( $item_id ) ) ) {
                $reset_get_rev = wp_get_post_revisions( $item_id );
                $item_id       = ( $rewisions = reset( $reset_get_rev ) ) && isset( $rewisions->ID ) ? $rewisions->ID : $item_id;
            }

        }

        if ( empty( $option_id ) ) {
            $sub_keys = null;
        } else {
            $option_id  = explode( '/', $option_id );                            // 'option_id/sub/keys'
            $_option_id = array_shift( $option_id );                             // 'option_id'
            $sub_keys   = empty( $option_id ) ? null : implode( '/', $option_id ); // 'sub/keys'
            $option_id  = $_option_id;
            unset( $_option_id );
        }

        try {

            $values = DEVM_Cache::get( $cache_key_values = $this->get_cache_key( 'values', $item_id, $extra_data ) );

        } catch ( DEVM_Cache_Not_Found_Exception $e ) {

            DEVM_Cache::set(
                $cache_key_values,
                $values = ( is_array( $values = $this->get_values( $item_id, $extra_data ) ) ? $values : [] )
            );
        }

        if ( !is_null( $default_value ) ) {

            if ( empty( $option_id ) ) {

                if ( empty( $values )
                    && (
                        is_array( $default_value )
                        ||
                        devm_is_callback( is_array( $default_value ) )
                    )
                ) {
                    return devm_call( $default_value );
                }

            } else {

                if ( is_null( $sub_keys ) ) {

                    if ( !isset( $values[$option_id] ) ) {
                        return devm_call( $default_value );
                    }

                } else {

                    if ( !isset( $values[$option_id] ) || is_null( devm_array_key_get( $sub_keys, $values[$option_id] ) ) ) {
                        return devm_call( $default_value );
                    }

                }

            }

        }

        try {
            $options = DEVM_Cache::get( $cache_key = $this->get_cache_key( 'options', $item_id, $extra_data ) );
        } catch ( DEVM_Cache_Not_Found_Exception $e ) {
            DEVM_Cache::set( $cache_key, [] ); // prevent recursion
            DEVM_Cache::set( $cache_key, $options = devm_extract_only_options( $this->get_options( $item_id, $extra_data ) ) );
        }

        if ( $options ) {
            try {
                DEVM_Cache::get(

                    $cache_key_values_processed = $this->get_cache_key( 'values:processed', $item_id, $extra_data )
                );
            } catch ( DEVM_Cache_Not_Found_Exception $e ) {

                DEVM_Cache::set( $cache_key_values_processed, true );

                {
                    try {
                        $skip_types_process = DEVM_Cache::get( $cache_key = 'devm:options-default-values:skip-types' );
                    } catch ( DEVM_Cache_Not_Found_Exception $e ) {
                        DEVM_Cache::set(
                            $cache_key,
                            $skip_types_process = apply_filters( 'devm:options-default-values:skip-types', [ // 'type' => true
                            ] )
                        );
                    }

                    foreach ( array_diff_key( devm_extract_only_options( $options ), $values ) as $id => $option ) {
                        $values[$id] = isset( $skip_types_process[$option['type']] )
                        ? (
                            isset( $option['value'] )
                            ? $option['value']
                            : devm()->backend->option_type( $option['type'] )->get_defaults( 'value' )
                        )
                        : devm()->backend->option_type( $option['type'] )->get_value_from_input( $option, null );
                    }

                }

                foreach ( $options as $id => $option ) {
                    $values[$id] = devm()->backend->option_type( $option['type'] )->storage_load(
                        $id,
                        $option,
                        isset( $values[$id] ) ? $values[$id] : null,
                        $this->get_devm_storage_params( $item_id, $extra_data )
                    );
                }

                DEVM_Cache::set( $cache_key_values, $values );
            }

        }

        if ( empty( $option_id ) ) {
            return ( empty( $values ) && ( is_array( $default_value ) || devm_is_callback( $default_value ) ) )
            ? devm_call( $default_value )
            : $values;
        } else {

            if ( is_null( $sub_keys ) ) {
                return isset( $values[$option_id] )
                ? $values[$option_id]
                : devm_call( $default_value );
            } else {
                return isset( $values[$option_id] )
                ? devm_array_key_get( $sub_keys, $values[$option_id], $default_value )
                : devm_call( $default_value );
            }

        }

    }

    final public function set( $item_id = null, $option_id = null, $value = '', array $extra_data = [] ) {
        DEVM_Cache::del( $cache_key_values = $this->get_cache_key( 'values', $item_id, $extra_data ) );
        DEVM_Cache::del( $cache_key_values_processed = $this->get_cache_key( 'values:processed', $item_id, $extra_data ) );

        try {
            $options = DEVM_Cache::get( $cache_key = $this->get_cache_key( 'options', $item_id, $extra_data ) );
        } catch ( DEVM_Cache_Not_Found_Exception $e ) {
            DEVM_Cache::set( $cache_key, [] ); // prevent recursion
            DEVM_Cache::set( $cache_key, $options = devm_extract_only_options( $this->get_options( $item_id, $extra_data ) ) );
        }

        $sub_keys = null;

        if ( $option_id ) {
            $option_id  = explode( '/', $option_id );                            // 'option_id/sub/keys'
            $_option_id = array_shift( $option_id );                             // 'option_id'
            $sub_keys   = empty( $option_id ) ? null : implode( '/', $option_id ); // 'sub/keys'
            $option_id  = $_option_id;
            unset( $_option_id );

            $old_values = is_array( $old_values = $this->get_values( $item_id, $extra_data ) ) ? $old_values : [];
            $old_value  = isset( $old_values[$option_id] ) ? $old_values[$option_id] : null;

            if ( $sub_keys ) { // update sub_key in old_value and use the entire value
                $new_value = $old_value;
                devm_array_key_set( $sub_keys, $value, $new_value );
                $value = $new_value;
                unset( $new_value );

                $old_value = devm_array_key_get( $sub_keys, $old_value );
            }

            if ( isset( $options[$option_id] ) ) {
                $value = devm()->backend->option_type( $options[$option_id]['type'] )->storage_save(
                    $option_id,
                    $options[$option_id],
                    $value,
                    $this->get_devm_storage_params( $item_id, $extra_data )
                );
            }

            $old_values[$option_id] = $value;

            $this->set_values( $item_id, $old_values, $extra_data );

            unset( $old_values );
        } else {
            $old_value = is_array( $old_values = $this->get_values( $item_id, $extra_data ) ) ? $old_values : [];

            if ( !is_array( $value ) ) {
                $value = [];
            }

            if ( empty( $value ) ) {

                foreach ( $options as $_option_id => $_option ) {
                    devm()->backend->option_type( $options[$_option_id]['type'] )->storage_save(
                        $_option_id,
                        $_option,
                        devm()->backend->option_type( $options[$_option_id]['type'] )->get_defaults( 'value' ),
                        $this->get_devm_storage_params( $item_id, $extra_data )
                    );
                }

            } else {

                foreach ( $value as $_option_id => $_option_value ) {

                    if ( isset( $options[$_option_id] ) ) {
                        $value[$_option_id] = devm()->backend->option_type( $options[$_option_id]['type'] )->storage_save(
                            $_option_id,
                            $options[$_option_id],
                            $_option_value,
                            $this->get_devm_storage_params( $item_id, $extra_data )
                        );
                    }

                }

            }

            $this->set_values( $item_id, $value, $extra_data );
        }

        DEVM_Cache::del( $cache_key_values );
        DEVM_Cache::del( $cache_key_values_processed );

        $this->after_set_protected( $item_id, $option_id, $sub_keys, $old_value, $extra_data );
    }

}
