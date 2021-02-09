<?php

defined( 'ABSPATH' ) || exit;

class Devm_Reset_DB {

    private $core_tables_to_truncate = [
        'commentmeta',
        'comments',
        'links',
        'postmeta',
        'posts',
        'term_relationships',
        'term_taxonomy',
        'termmeta',
        'terms',
    ];

    /**
     * Handle all functions for resetting database
     */
    function devm_reset_previous_data() {
        $this->truncate_tables();
        $this->delete_transients();
        $this->reset_theme_options();
        $this->flush_sidebar_widgets();
    }

    /**
     * Truncate custom tables
     */
    function truncate_tables() {
        global $wpdb;
        $tables = $this->core_tables_to_truncate;

        foreach ( $tables as $tbl ) {
            $wpdb->query( 'SET foreign_key_checks = 0' );
            $wpdb->query( 'TRUNCATE TABLE ' . $wpdb->prefix . $tbl );
        }
    }

    /**
     * Deletes all transients.
     */
    function delete_transients() {
        global $wpdb;

        $count = $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '\_transient\_%' OR option_name LIKE '\_site\_transient\_%'" );

        wp_cache_flush();

        do_action( 'devm_delete_transients', $count );
    }

    /**
     * Resets all theme options (mods).
     *
     */
    function reset_theme_options( $all_themes = true ) {
        global $wpdb;

        $count = $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'theme_mods\_%' OR option_name LIKE 'mods\_%'" );

        do_action( 'devm_reset_theme_options', $count );
    }

    function flush_sidebar_widgets() {
        $sidebars_widgets = get_option( 'sidebars_widgets' );
        if( is_array( $sidebars_widgets )){
            foreach( $sidebars_widgets as  $sidebar => $widgets ){
                if( is_array( $widgets ) ) {
                    $sidebars_widgets[$sidebar] = [];
                }
            }
        }
        update_option( 'sidebars_widgets', $sidebars_widgets );
    }

    /**
     * Drop custom tables
     */
    function do_drop_tables() {
        global $wpdb;
        $tables_drop = $this->core_tables_to_truncate;

        foreach ( $tables_drop as $tbl ) {
            $wpdb->query( 'SET foreign_key_checks = 0' );
            $wpdb->query( 'DROP TABLE IF EXISTS ' . $tbl );
        }

        do_action( 'devm_drop_custom_tables', $tables_drop );

        return sizeof( $tables_drop );
    }

}

// $reset_db_obj = new Devm_Reset_DB();
// $reset_db_obj->devm_reset_previous_data();
