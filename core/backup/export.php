<?php

/**
 * Export Elementor style files
 *
 * @return void
 */
function devm_export_elementor_css_file() {
    try {
        $uploads = wp_upload_dir();
        $uploads['baseurl'] . "/elementor/css/";
        $path      = $uploads['baseurl'] . "/elementor/css/";
        $directory = devm_backups_destination_directory();

        if ( is_dir( $directory ) ) {
            $all_files = glob( $directory . '/*.css' );

            foreach ( $all_files as $file ):
                $file_url = devm_fix_path( $uploads['baseurl'] . "/elementor/css" ) . "/" . basename( $file );
                ?>
	                <wp:elementor>
	                    <wp:path><?php echo esc_url( $file_url ); ?></wp:path>
	                </wp:elementor>
	            <?php
            endforeach;
        }

    } catch ( Exception $e ) {
        trigger_error( 'Caught exception elementor file: ' . $e->getMessage() );
    }

}

/**
 * Export customizer data
 *
 * @return void
 */
function devm_export_option_file() {
    $theme_name  = strtolower( get_option( 'current_theme' ) );
    $option_name = "theme_mods_" . $theme_name;
    global $wpdb, $table_prefix;
    $customizer_serialized_data = $wpdb->get_var( 'SELECT option_value FROM ' . $table_prefix . 'options WHERE option_name LIKE "%' . $option_name . '%"' );
    ?>
    <wp:customizer>
        <wp:theme>
            <wp:title><?php echo esc_html( $theme_name ); ?></wp:title>
            <wp:option><?php echo devm_render_markup( $customizer_serialized_data ); ?></wp:option>
        </wp:theme>
    </wp:customizer>
    <?php
}

/**
 * Export menu data
 *
 * @return void
 */
function devm_export_primary_menu_slug() {
    $menu_name         = 'primary';
    $locations         = get_nav_menu_locations();
    $menu_id           = $locations[$menu_name];
    $primary_menu_slug = wp_get_nav_menu_object( $menu_id )->slug;
    ?>
    <menu>
        <primary>
            <slug><?php echo devm_render_markup( $primary_menu_slug ); ?></slug>
        </primary>
    </menu>
    <?php
}

/**
 * Export widget data
 *
 * @return void
 */
function devm_export_widget_option() {
    $data = devm_widgets_export();
    ?>
    <wp:sidebar>
        <wp:widgets><?php echo esc_html( $data ); ?></wp:widgets>
    </wp:sidebar>
    <?php
}

/**
 * Export site settings
 *
 * @return void
 */
function devm_export_settings() {
    $page_on_front_settings = get_option( "page_on_front", "0" );
    $page_slug_value        = get_post_field( 'post_name', $page_on_front_settings );
    $show_on_front_value    = get_option( "show_on_front", "page" );
    $content_url            = content_url();
    $site_url               = get_site_url();
    ?>
    <settings>
        <page_on_front><?php echo devm_cdata( $page_slug_value ); ?></page_on_front>
        <show_on_front><?php echo devm_cdata( $show_on_front_value ); ?></show_on_front>
    </settings>
    <wp:base_details>
        <wp:site_url><?php echo devm_cdata( $site_url ); ?></wp:site_url>
        <wp:content_url><?php echo devm_cdata( $content_url ); ?></wp:content_url>
    </wp:base_details>
    <?php
}

add_action( "rss2_head", "devm_export_elementor_css_file" );
add_action( "rss2_head", "devm_export_option_file" );
add_action( "rss2_head", "devm_export_primary_menu_slug" );
add_action( "rss2_head", "devm_export_widget_option" );
add_action( "rss2_head", "devm_export_settings" );
