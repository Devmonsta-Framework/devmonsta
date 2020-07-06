<?php

defined( 'ABSPATH' ) || exit;

class Devm_Plugin_Backup_Restore {

    public $pluginsInstall = [];

    function devm_get_all_installed_plugins_directory() {

        if ( !function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugin_main_file_path = [];
        $all_plugins           = get_plugins();

        foreach ( $all_plugins as $key => $value ) {
            array_push( $plugin_main_file_path, $key );
        }

        return $plugin_main_file_path;
    }

    function devm_get_all_installed_plugins_slug() {

        if ( !function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugin_slugs = [];
        $all_plugins  = get_plugins();

        foreach ( $all_plugins as $key => $value ) {
            $key_parts = explode( "/", $key );
            array_push( $plugin_slugs, $key_parts[0] );
        }

        return $plugin_slugs;
    }

    /**
     * Get all active plugins
     * Insert plugin slug into exported xml file in the below format
     */
    function devm_backup_plugins() {
        $active_plugins = get_option( 'active_plugins' );
        ?>
        <wp:plugins>
            <?php
        foreach ( $active_plugins as $plugin ) {?>
                <wp:slug><?php echo DEVM_Helpers::render( $plugin ); ?></wp:slug>
            <?php }
        ?>
        </wp:plugins>
    <?php
    }

    /**
     * Check if a specific plugin is installed in system
     * Check is done using plugin's slug
     */
    function devm_is_plugin_installed( $slug ) {

        if ( !function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $all_plugins = get_plugins();

        if ( !empty( $all_plugins[$slug] ) ) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * takes plugin slug as parameter and installs plugin
     */
    function devm_install_plugin( $plugin_slug ) {
        include_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
        require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';

        $api = plugins_api(
            'plugin_information',
            [
                'slug'   => $plugin_slug,
                'fields' => [
                    'short_description' => false,
                    'sections'          => false,
                    'requires'          => false,
                    'rating'            => false,
                    'ratings'           => false,
                    'downloaded'        => true,
                    'last_updated'      => false,
                    'added'             => false,
                    'tags'              => false,
                    'compatibility'     => false,
                    'homepage'          => false,
                    'donate_link'       => false,
                ],
            ]
        );
        $skin     = new WP_Ajax_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader( $skin );
        $upgrader->install( $api->download_link );
    }

    function devm_upgrade_plugin( $plugin_slug ) {

        include_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
        require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';

        $api = plugins_api(
            'plugin_information',
            [
                'slug'   => $plugin_slug,
                'fields' => [
                    'short_description' => false,
                    'sections'          => false,
                    'requires'          => false,
                    'rating'            => false,
                    'ratings'           => false,
                    'downloaded'        => true,
                    'last_updated'      => false,
                    'added'             => false,
                    'tags'              => false,
                    'compatibility'     => false,
                    'homepage'          => false,
                    'donate_link'       => false,
                ],
            ]
        );
        $skin     = new WP_Ajax_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader( $skin );
        $upgrader->upgrade( $api->download_link );
    }

    function devm_process_plugins( $plugins_array = "" ) {
        $plugins_array = apply_filters( 'devm_import_activated_plugins', $plugins_array );

        if ( is_array( $plugins_array ) && !empty( $plugins_array ) ) {
            $installed_plugin_slug_array = $this->devm_get_all_installed_plugins_slug();
            try {

                foreach ( $plugins_array as $plugin_slug ) {
                    $installed                          = false;
                    $this->pluginsInstall[$plugin_slug] = $installed;

                    if ( !in_array( $plugin_slug, $installed_plugin_slug_array ) ) {
                        //plugin's not installed, install plugin
                        $this->devm_install_plugin( $plugin_slug );
                        $installed                          = true;
                        $this->pluginsInstall[$plugin_slug] = $installed;
                    } 
                    // else {
                    //     //plugin's already installed, upgrade plugin
                    //     $this->devm_upgrade_plugin( $plugin_slug );
                    //     $installed                          = true;
                    //     $this->pluginsInstall[$plugin_slug] = $installed;
                    // }

                    $plugin_slug                    = $plugin_slug;
                    $updated_plugin_directory_array = $this->devm_get_all_installed_plugins_directory();
                    $updated_plugin_slug_array      = $this->devm_get_all_installed_plugins_slug();
                    $current_slug_index             = array_search( $plugin_slug, $updated_plugin_slug_array );
                    $current_slug_main_path         = $updated_plugin_directory_array[$current_slug_index];

                    if ( $installed && !is_wp_error( $installed ) && !is_plugin_active( $current_slug_main_path ) ) {
                        activate_plugin( $current_slug_main_path );
                    }

                }

                return $this->pluginsInstall;
            } catch ( Exception $ex ) {
                return $this->pluginsInstall;
            }

        }

    }

}

// $devm_plugin_obj = new Devm_Plugin_Backup_Restore();
// devm_print($devm_plugin_obj->devm_get_all_installed_plugins_directory());
