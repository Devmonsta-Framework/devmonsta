<?php

function dm_export_elementor_css_file()
{
    try {
        $uploads = wp_upload_dir();
        $uploads['baseurl'] .   "/elementor/css/";
        $path =  $uploads['baseurl'] .   "/elementor/css/";
        $directory = dm_backups_destination_directory();
        if (is_dir($directory)) {
            $all_files = glob($directory . '/*.css');
            foreach ($all_files as $file) :
                $file_url =  dm_fix_path($uploads['baseurl'] .   "/elementor/css") . "/" . basename($file);
            ?>
                <wp:elementor>
                    <wp:path><?php echo esc_url($file_url); ?></wp:path>
                </wp:elementor>
            <?php
            endforeach;
        }
    } catch (Exception $e) {
        trigger_error('Caught exception elementor file: ' . $e->getMessage());
    }
}

add_action("rss2_head", "dm_export_elementor_css_file");

function dm_export_option_file()
{
    $theme_name =    strtolower(get_option('current_theme'));
    $option_name = "theme_mods_" . $theme_name;
    global $wpdb, $table_prefix;
    $customizer_serialized_data = $wpdb->get_var('SELECT option_value FROM ' . $table_prefix . 'options WHERE option_name LIKE "%' . $option_name . '%"');
    ?>
    <wp:customizer>
        <wp:theme>
            <wp:title><?php echo esc_html($theme_name); ?></wp:title>
            <wp:option><?php echo dm_render_markup($customizer_serialized_data); ?></wp:option>
        </wp:theme>
    </wp:customizer>
<?php
}

add_action("rss2_head", "dm_export_option_file");

function dm_export_primary_menu_slug()
{
    $menu_name = 'primary';
    $locations = get_nav_menu_locations();
    $menu_id   = $locations[$menu_name];
    $primary_menu_slug = wp_get_nav_menu_object($menu_id)->slug;
    ?>
    <menu>
        <primary>
            <slug><?php echo dm_render_markup($primary_menu_slug); ?></slug>
        </primary>
    </menu>
    <?php
}

add_action("rss2_head", "dm_export_primary_menu_slug");


function dm_export_widget_option()
{
    $data = dm_widgets_export();
    ?>
    <wp:sidebar>
        <wp:widgets><?php echo esc_html($data); ?></wp:widgets>
    </wp:sidebar>
    <?php
}

add_action("rss2_head", "dm_export_widget_option");