<?php

if ( !class_exists( 'WP_Importer' ) ) {
    return;
}

/** Display verbose errors */
define( 'IMPORT_DEBUG', false );

// Include WXR file parsers.
require dirname( __FILE__ ) . '/class-wxr-parsers.php';
require_once ABSPATH . 'wp-admin/includes/post.php';
require_once ABSPATH . 'wp-admin/includes/comment.php';
require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

class Devm_WXR_Importer extends WP_Importer {
    var $max_wxr_version = 1.2; // max. supported WXR version

    var $id;
    // WXR attachment ID

    // information to import from WXR file
    var $version;
    var $authors                = [];
    var $posts                  = [];
    var $terms                  = [];
    var $categories             = [];
    var $tags                   = [];
    var $customizers            = [];
    var $widget_sidebars        = [];
    var $elementors             = [];
    var $base_url               = '';
    var $base_content_url       = '';
    var $base_site_url          = '';
    var $time_slots             = [];

    // mappings from old information to new
    var $processed_authors    = [];
    var $author_mapping       = [];
    var $processed_terms      = [];
    var $processed_posts      = [];
    var $post_orphans         = [];
    var $processed_menu_items = [];
    var $menu_item_orphans    = [];
    var $missing_menu_items   = [];

    var $fetch_attachments = false;
    var $url_remap         = [];
    var $featured_images   = [];

    /**
     * The main controller for the actual import stage.
     *
     * @param string $file Path to the WXR file for importing
     */
    function import( $file, $selected_demo_array = [] ) {
        add_filter( 'import_post_meta_key', [ $this, 'is_valid_meta_key' ] );
        add_filter( 'http_request_timeout', [ &$this, 'bump_request_timeout' ] );

        //Executes before starting import action.
        do_action('devm_before_import_execution_start');

        // Execute the before all import actions.
        $this->import_start( $file );

        $this->get_author_mapping();

        wp_suspend_cache_invalidation( true );
        
        $this->process_widgets_sidebar();
        $this->process_categories();
        $this->process_tags();
        $this->process_terms();
        $this->process_elementor_css();
        $this->process_customizers();
        $this->process_posts();
		$this->process_external_modules( $selected_demo_array );
        $this->update_reading_setting( $file );
        $this->devm_update_primary_menu( $file );

        wp_suspend_cache_invalidation( false );

        // update incorrect/missing information in the DB
        $this->backfill_parents();
        $this->backfill_attachment_urls();
        $this->remap_featured_images();

        // Execute the after all import actions.
        $this->import_end();

        //Executes after ending import action.
        do_action('devm_after_import_execution_end', $selected_demo_array );
       
    }

    /**
     * process all external plugin data
     */
    public function process_external_modules( $selected_demo_array ){

        $external_modules = !empty( $selected_demo_array['modules'] ) ? $selected_demo_array['modules'] : [];

        if( is_array( $external_modules ) && !empty( $external_modules ) ){
            foreach( $external_modules as $key => $value ){

                if( !empty($value['src'] ) ){
                    $this->import_module( $key, $value['src'] );
                }
            }
        }
        
        //check and import mp-timetable plugin data
        if ( $this->check_if_plugin_active( 'mp-timetable/mp-timetable.php' ) ) {
            $this->import_mptimetable_data();
        }
    }

    public function import_module($module_name, $module_source){
        $file_name      = DEVMONSTA_DIR . '/core/helpers/backup/inc/modules/'.$module_name.'.php';
        $class_name     = 'Devmonsta\Core\Helpers\Backup\Inc\Modules\\' . ucfirst($module_name);
        if( file_exists( $file_name ) ){

            include $file_name;

            if(class_exists( $class_name )){
                $module_class = new $class_name;
                $module_class->set_source( $module_source )->process_data();
            }
        }
    }

    /**
     * process timeshot plugin data
     *
     * @return void
     */
	public function import_mptimetable_data() {
		global $wpdb;
        $table_name = $wpdb->prefix . "mp_timetable_data";
		$rows_affected = array();
        $time_slots = $this->time_slots;
		if ( !empty($time_slots) ) {
			foreach ( $time_slots as $time_slot ) {
				$exist_time_slot = $this->post_time_slot_exist($time_slot);
				if (!$exist_time_slot) {
					$rows_affected[] = $wpdb->insert($table_name, array(
						'column_id' => $time_slot['column'],
						'event_id' => $time_slot['event'],
						'event_start' => date('H:i:s', strtotime($time_slot['event_start'])),
						'event_end' => date('H:i:s', strtotime($time_slot['event_end'])),
						'user_id' => $time_slot['user_id'],
						'description' => $time_slot['description']
					));
				}
			}
		}
    }
    
    /**
     * Check if specific plugin is active
     */
    public function check_if_plugin_active( $slug ){
        $active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
        $plugin_to_check = $slug;
        if ( in_array( $plugin_to_check, $active_plugins ) ) {
            return true;
        }

        return false;
    }
    

	/**
	 * Exist time slot
	 *
	 * @param array $time_slot
	 *
	 * @return bool
	 */
	public function post_time_slot_exist($time_slot = array()) {
        global $wpdb;
        $table_name = $wpdb->prefix . "mp_timetable_data";

		if (empty($time_slot)) {
			return false;
		}
		$data = $wpdb->get_results('SELECT id FROM  ' . $table_name . '   WHERE column_id = "' . $time_slot['column'] . '" AND event_id = "' . $time_slot['event'] . '" AND event_start = "' . $time_slot['event_start'] . '" AND event_end = "' . $time_slot['event_end'] . '"');
		return empty($data) ? false : true;
    }
    

    /**
     * Update reading settings value
     */
    function update_reading_setting( $file ) {
        $xml_file = simplexml_load_file( $file );

        if ( property_exists( $xml_file->channel, "settings" ) ) {
            $settings = $xml_file->channel->settings;

            $page_slug_on_front = $settings->page_on_front;
            $show_on_front = $settings->show_on_front;
            $page_id = $this->devm_get_id_by_slug( $page_slug_on_front, 'page' );
            
            update_option( "page_on_front", $page_id );
            update_option( "show_on_front", $show_on_front."" );
        }
    }

    public function devm_update_primary_menu( $file ) {
        $xml_file          = simplexml_load_file( $file );
        $all_nodes         = $xml_file->channel;
        $primary_menu_slug = $all_nodes->menu->primary->slug;

        $menu    = get_term_by( 'slug', $primary_menu_slug, 'nav_menu' );
        $menu_id = $menu->term_id;

        $location                     = "primary";
        $nav_menu_location            = get_theme_mod( 'nav_menu_locations' );
        $nav_menu_location[$location] = $menu_id;

        set_theme_mod( 'nav_menu_locations', $nav_menu_location );
    }

    function devm_get_id_by_slug( $title, $type ) {
        $page = get_posts(
            [
                'name'        => $title,
                'post_type'   => $type,
                'numberposts' => 1,
            ]
        );
        return $page[0]->ID;
    }

    /**
     * Parses the WXR file and prepares us for the task of processing parsed data
     *
     * @param string $file Path to the WXR file for importing
     */
    function import_start( $file ) {

        if ( !is_file( $file ) ) {
            echo '<p><strong>' . __( 'Sorry, there has been an error.', 'devmonsta' ) . '</strong><br />';
            echo __( 'The file does not exist, please try again.', 'devmonsta' ) . '</p>';

            die();
        }

        //$import_data holds array of paresed data from xml file
        $import_data = $this->parse( $file );

        if ( is_wp_error( $import_data ) ) {
            echo '<p><strong>' . __( 'Sorry, there has been an error.', 'devmonsta' ) . '</strong><br />';
            echo esc_html( $import_data->get_error_message() ) . '</p>';

            die();
        }

        $this->version = $import_data['version'];
        $this->get_authors_from_import( $import_data );
        $this->terms           = $import_data['terms'];
        $this->posts           = $import_data['posts'];
        $this->categories      = $import_data['categories'];
        $this->tags            = $import_data['tags'];
        $this->elementors      = $import_data['elementor'];
        $this->customizers     = $import_data['theme_mod_array'];
        $this->sidebar_widgets = $import_data['sidebar_widgets'];
        $this->base_url        = esc_url( $import_data['base_url'] );
        $this->base_site_url   = esc_url( $import_data['site_url'] );
        $this->base_content_url= esc_url( $import_data['content_url'] );

        if( $this->check_if_plugin_active('mp-timetable/mp-timetable.php') ){
            $this->time_slots      = isset( $import_data['time_slots'] ) ? $import_data['time_slots'] : [];
        }

        wp_defer_term_counting( true );
        wp_defer_comment_counting( true );

        do_action( 'import_start' );
    }

    /**
     * Performs post-import cleanup of files and the cache
     */
    function import_end() {
        wp_import_cleanup( $this->id );

        wp_cache_flush();

        foreach ( get_taxonomies() as $tax ) {
            delete_option( "{$tax}_children" );
            _get_term_hierarchy( $tax );
        }

        wp_defer_term_counting( false );
        wp_defer_comment_counting( false );

        do_action( 'import_end' );
    }

    function get_authors_from_import( $import_data ) {

        if ( !empty( $import_data['authors'] ) ) {
            $this->authors = $import_data['authors'];
            // no author information, grab it from the posts
        } else {

            foreach ( $import_data['posts'] as $post ) {
                $login = sanitize_user( $post['post_author'], true );

                if ( empty( $login ) ) {
                    printf( __( 'Failed to import author %s. Their posts will be attributed to the current user.', 'devmonsta' ), esc_html( $post['post_author'] ) );
                    echo '<br />';
                    continue;
                }

                if ( !isset( $this->authors[$login] ) ) {
                    $this->authors[$login] = [
                        'author_login'        => $login,
                        'author_display_name' => $post['post_author'],
                    ];
                }

            }

        }

    }

    function get_author_mapping() {

        if ( !isset( $_POST['imported_authors'] ) ) {
            return;
        }

        $create_users = $this->allow_create_users();

        foreach ( (array) $_POST['imported_authors'] as $i => $old_login ) {
            // Multisite adds strtolower to sanitize_user. Need to sanitize here to stop breakage in process_posts.
            $santized_old_login = sanitize_user( $old_login, true );
            $old_id             = isset( $this->authors[$old_login]['author_id'] ) ? intval( $this->authors[$old_login]['author_id'] ) : false;

            if ( !empty( $_POST['user_map'][$i] ) ) {
                $user = get_userdata( intval( $_POST['user_map'][$i] ) );

                if ( isset( $user->ID ) ) {

                    if ( $old_id ) {
                        $this->processed_authors[$old_id] = $user->ID;
                    }

                    $this->author_mapping[$santized_old_login] = $user->ID;
                }

            } elseif ( $create_users ) {

                if ( !empty( $_POST['user_new'][$i] ) ) {
                    $user_id = wp_create_user( $_POST['user_new'][$i], wp_generate_password() );
                } elseif ( $this->version != '1.0' ) {
                    $user_data = [
                        'user_login'   => $old_login,
                        'user_pass'    => wp_generate_password(),
                        'user_email'   => isset( $this->authors[$old_login]['author_email'] ) ? $this->authors[$old_login]['author_email'] : '',
                        'display_name' => $this->authors[$old_login]['author_display_name'],
                        'first_name'   => isset( $this->authors[$old_login]['author_first_name'] ) ? $this->authors[$old_login]['author_first_name'] : '',
                        'last_name'    => isset( $this->authors[$old_login]['author_last_name'] ) ? $this->authors[$old_login]['author_last_name'] : '',
                    ];
                    $user_id = wp_insert_user( $user_data );
                }

                if ( !is_wp_error( $user_id ) ) {

                    if ( $old_id ) {
                        $this->processed_authors[$old_id] = $user_id;
                    }

                    $this->author_mapping[$santized_old_login] = $user_id;
                } else {
                    printf( __( 'Failed to create new user for %s. Their posts will be attributed to the current user.', 'devmonsta' ), esc_html( $this->authors[$old_login]['author_display_name'] ) );

                    if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
                        echo ' ' . esc_html( $user_id->get_error_message() );
                    }

                    echo '<br />';
                }

            }

            // failsafe: if the user_id was invalid, default to the current user
            if ( !isset( $this->author_mapping[$santized_old_login] ) ) {
                if ( $old_id ) {
                    $this->processed_authors[$old_id] = (int) get_current_user_id();
                }

                $this->author_mapping[$santized_old_login] = (int) get_current_user_id();
            }

        }

    }

    function process_elementor_css() {

        $this->elementors = apply_filters( 'wp_import_elementor_css', $this->elementors );
        if ( !is_array( $this->elementors ) ) {
            return;
        }

        if ( !count( $this->elementors ) ) {
            return;
        }

    }

    function process_customizers() {
        $this->customizers = apply_filters( 'devm_import_customizer', $this->customizers );
        if ( is_array( $this->customizers ) ) {
            $customizer_data = unserialize( $this->customizers[1] );
            $theme_name      = $this->customizers[0];
            if ( empty( $customizer_data ) ) {
                return;
            }

            $option_name = "theme_mods_" . $theme_name;
            update_option( $option_name, $customizer_data );
        }

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

    function devm_install_plugin( $plugin_zip ) {
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        wp_cache_flush();

        $upgrader  = new Plugin_Upgrader();
        $installed = $upgrader->install( $plugin_zip );

        return $installed;
    }

    function devm_upgrade_plugin( $plugin_slug ) {
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        wp_cache_flush();

        $upgrader = new Plugin_Upgrader();
        $upgraded = $upgrader->upgrade( $plugin_slug );

        return $upgraded;
    }

    function process_plugins() {
        $install_report          = "";
        $this->activated_plugins = apply_filters( 'devm_import_activated_plugins', $this->activated_plugins );

        if ( is_array( $this->activated_plugins ) ) {
            foreach ( $this->activated_plugins as $plugin_slug ) {
                $slug_words = explode( "/", $plugin_slug );
                $plugin_zip = "https://downloads.wordpress.org/plugin" . $slug_words[0] . ".latest-stable.zip";
                $installed  = false;
                if ( devm_is_plugin_installed( $plugin_slug ) ) {
                    $this->devm_upgrade_plugin( $plugin_slug );
                    $installed = true;
                    $install_report .= "upgraded";
                } else {
                    $this->devm_install_plugin( $plugin_zip );
                    $installed = true;
                    $install_report .= "installed";
                }

                if ( $installed && !is_wp_error( $installed ) && !is_plugin_active( $plugin_slug ) ) {
                    activate_plugin( $plugin_slug );
                    $install_report .= "activated";
                }

            }

        }

    }

    function process_widgets_sidebar() {

        $this->sidebar_widgets = apply_filters( 'devm_import_widget_sidebars', $this->sidebar_widgets );
        if ( empty( $this->sidebar_widgets ) ) {
            return;
        }

        devm_widgets_import_data( json_decode( $this->sidebar_widgets ) );
    }

    function process_categories() {
        $this->categories = apply_filters( 'wp_import_categories', $this->categories );

        if ( empty( $this->categories ) ) {
            return;
        }

        foreach ( $this->categories as $cat ) {
            // if the category already exists leave it alone
            $term_id = term_exists( $cat['category_nicename'], 'category' );

            if ( $term_id ) {

                if ( is_array( $term_id ) ) {
                    $term_id = $term_id['term_id'];
                }

                if ( isset( $cat['term_id'] ) ) {
                    $this->processed_terms[intval( $cat['term_id'] )] = (int) $term_id;
                }

                continue;
            }

            $category_parent      = empty( $cat['category_parent'] ) ? 0 : category_exists( $cat['category_parent'] );
            $category_description = isset( $cat['category_description'] ) ? $cat['category_description'] : '';
            $catarr               = [
                'category_nicename'    => $cat['category_nicename'],
                'category_parent'      => $category_parent,
                'cat_name'             => $cat['cat_name'],
                'category_description' => $category_description,
            ];
            $catarr = wp_slash( $catarr );

            $id = wp_insert_category( $catarr );

            if ( !is_wp_error( $id ) ) {

                if ( isset( $cat['term_id'] ) ) {
                    $this->processed_terms[intval( $cat['term_id'] )] = $id;
                }

            } else {
                printf( __( 'Failed to import category %s', 'devmonsta' ), esc_html( $cat['category_nicename'] ) );

                if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
                    echo ': ' . esc_html( $id->get_error_message() );
                }

                echo '<br />';
                continue;
            }

            $this->process_termmeta( $cat, $id['term_id'] );
        }

        unset( $this->categories );
    }

    function process_tags() {
        $this->tags = apply_filters( 'wp_import_tags', $this->tags );

        if ( empty( $this->tags ) ) {
            return;
        }

        foreach ( $this->tags as $tag ) {
            // if the tag already exists leave it alone
            $term_id = term_exists( $tag['tag_slug'], 'post_tag' );

            if ( $term_id ) {

                if ( is_array( $term_id ) ) {
                    $term_id = $term_id['term_id'];
                }

                if ( isset( $tag['term_id'] ) ) {
                    $this->processed_terms[intval( $tag['term_id'] )] = (int) $term_id;
                }

                continue;
            }

            $tag      = wp_slash( $tag );
            $tag_desc = isset( $tag['tag_description'] ) ? $tag['tag_description'] : '';
            $tagarr   = [
                'slug'        => $tag['tag_slug'],
                'description' => $tag_desc,
            ];

            $id = wp_insert_term( $tag['tag_name'], 'post_tag', $tagarr );

            if ( !is_wp_error( $id ) ) {

                if ( isset( $tag['term_id'] ) ) {
                    $this->processed_terms[intval( $tag['term_id'] )] = $id['term_id'];
                }

            } else {
                printf( __( 'Failed to import post tag %s', 'devmonsta' ), esc_html( $tag['tag_name'] ) );

                if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
                    echo ': ' . esc_html( $id->get_error_message() );
                }

                echo '<br />';
                continue;
            }

            $this->process_termmeta( $tag, $id['term_id'] );
        }

        unset( $this->tags );
    }

    function process_terms() {
        $this->terms = apply_filters( 'wp_import_terms', $this->terms );

        if ( empty( $this->terms ) ) {
            return;
        }

        foreach ( $this->terms as $term ) {
            // if the term already exists in the correct taxonomy leave it alone
            $term_id = term_exists( $term['slug'], $term['term_taxonomy'] );

            if ( $term_id ) {

                if ( is_array( $term_id ) ) {
                    $term_id = $term_id['term_id'];
                }

                if ( isset( $term['term_id'] ) ) {
                    $this->processed_terms[intval( $term['term_id'] )] = (int) $term_id;
                }

                continue;
            }

            if ( empty( $term['term_parent'] ) ) {
                $parent = 0;
            } else {
                $parent = term_exists( $term['term_parent'], $term['term_taxonomy'] );

                if ( is_array( $parent ) ) {
                    $parent = $parent['term_id'];
                }

            }

            $term        = wp_slash( $term );
            $description = isset( $term['term_description'] ) ? $term['term_description'] : '';
            $termarr     = [
                'slug'        => $term['slug'],
                'description' => $description,
                'parent'      => intval( $parent ),
            ];

            $id = wp_insert_term( $term['term_name'], $term['term_taxonomy'], $termarr );

            if ( !is_wp_error( $id ) ) {

                if ( isset( $term['term_id'] ) ) {
                    $this->processed_terms[intval( $term['term_id'] )] = $id['term_id'];
                }

            } else {
                printf( __( 'Failed to import %1$s %2$s', 'devmonsta' ), esc_html( $term['term_taxonomy'] ), esc_html( $term['term_name'] ) );

                if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
                    echo ': ' . esc_html( $id->get_error_message() );
                }

                echo '<br />';
                continue;
            }

            $this->process_termmeta( $term, $id['term_id'] );
        }

        unset( $this->terms );
    }

    protected function process_termmeta( $term, $term_id ) {

        if ( !isset( $term['termmeta'] ) ) {
            $term['termmeta'] = [];
        }

        /**
         * Filters the metadata attached to an imported term.
         *
         * @since 0.6.2
         *
         * @param array $termmeta Array of term meta.
         * @param int   $term_id  ID of the newly created term.
         * @param array $term     Term data from the WXR import.
         */
        $term['termmeta'] = apply_filters( 'wp_import_term_meta', $term['termmeta'], $term_id, $term );

        if ( empty( $term['termmeta'] ) ) {
            return;
        }

        foreach ( $term['termmeta'] as $meta ) {
            /**
             * Filters the meta key for an imported piece of term meta.
             *
             * @since 0.6.2
             *
             * @param string $meta_key Meta key.
             * @param int    $term_id  ID of the newly created term.
             * @param array  $term     Term data from the WXR import.
             */
            $key = apply_filters( 'import_term_meta_key', $meta['key'], $term_id, $term );

            if ( !$key ) {
                continue;
            }

            // Export gets meta straight from the DB so could have a serialized string
            $value = maybe_unserialize( $meta['value'] );

            add_term_meta( $term_id, $key, $value );

            /**
             * Fires after term meta is imported.
             *
             * @since 0.6.2
             *
             * @param int    $term_id ID of the newly created term.
             * @param string $key     Meta key.
             * @param mixed  $value   Meta value.
             */
            do_action( 'import_term_meta', $term_id, $key, $value );
        }

    }

    function process_posts() {
        $this->posts = apply_filters( 'wp_import_posts', $this->posts );

        foreach ( $this->posts as $post ) {
            $post = apply_filters( 'wp_import_post_data_raw', $post );

            if ( !post_type_exists( $post['post_type'] ) ) {
                printf(
                    __( 'Failed to import &#8220;%s&#8221;: Invalid post type %s', 'wordpress-importer' ),
                    esc_html( $post['post_title'] ),
                    esc_html( $post['post_type'] )
                );
                echo '<br />';
                do_action( 'wp_import_post_exists', $post );
                continue;
            }

            if ( isset( $this->processed_posts[$post['post_id']] ) && !empty( $post['post_id'] ) ) {
                continue;
            }

            if ( $post['status'] == 'auto-draft' ) {
                continue;
            }

            if ( 'nav_menu_item' == $post['post_type'] ) {
                $this->process_menu_item( $post );
                continue;
            }

            $post_type_object = get_post_type_object( $post['post_type'] );

            $post_exists = post_exists( $post['post_title'], '', $post['post_date'] );

            $post_exists = apply_filters( 'wp_import_existing_post', $post_exists, $post );

            if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
                printf( __( '%1$s &#8220;%2$s&#8221; already exists.', 'devmonsta' ), $post_type_object->labels->singular_name, esc_html( $post['post_title'] ) );
                echo '<br />';
                $comment_post_ID                                 = $post_id                                 = $post_exists;
                $this->processed_posts[intval( $post['post_id'] )] = intval( $post_exists );
            } else {
                $post_parent = (int) $post['post_parent'];

                if ( $post_parent ) {

                    // if we already know the parent, map it to the new local ID
                    if ( isset( $this->processed_posts[$post_parent] ) ) {
                        $post_parent = $this->processed_posts[$post_parent];
                        // otherwise record the parent for later
                    } else {
                        $this->post_orphans[intval( $post['post_id'] )] = $post_parent;
                        $post_parent                                  = 0;
                    }

                }

                // map the post author
                $author = sanitize_user( $post['post_author'], true );

                if ( isset( $this->author_mapping[$author] ) ) {
                    $author = $this->author_mapping[$author];
                } else {
                    $author = (int) get_current_user_id();
                }

                $postdata = [
                    'import_id'      => $post['post_id'],
                    'post_author'    => $author,
                    'post_date'      => $post['post_date'],
                    'post_date_gmt'  => $post['post_date_gmt'],
                    'post_content'   => $post['post_content'],
                    'post_excerpt'   => $post['post_excerpt'],
                    'post_title'     => $post['post_title'],
                    'post_status'    => $post['status'],
                    'post_name'      => $post['post_name'],
                    'comment_status' => $post['comment_status'],
                    'ping_status'    => $post['ping_status'],
                    'guid'           => $post['guid'],
                    'post_parent'    => $post_parent,
                    'menu_order'     => $post['menu_order'],
                    'post_type'      => $post['post_type'],
                    'post_password'  => $post['post_password'],
                ];

                $original_post_ID = $post['post_id'];
                $postdata         = apply_filters( 'wp_import_post_data_processed', $postdata, $post );

                $postdata = wp_slash( $postdata );

                if ( 'attachment' == $postdata['post_type'] ) {

                    $remote_url = !empty( $post['attachment_url'] ) ? $post['attachment_url'] : $post['guid'];

                    // try to use _wp_attached file for upload folder placement to ensure the same location as the export site
                    // e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload()
                    $postdata['upload_date'] = $post['post_date'];

                    if ( isset( $post['postmeta'] ) ) {

                        foreach ( $post['postmeta'] as $meta ) {

                            if ( $meta['key'] == '_wp_attached_file' ) {

                                if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta['value'], $matches ) ) {
                                    $postdata['upload_date'] = $matches[0];
                                }

                                break;
                            }

                        }

                    }
                    $comment_post_ID = $post_id = $this->process_attachment( $postdata, $remote_url );
                } else {
                    $comment_post_ID = $post_id = wp_insert_post( $postdata, true );
                    do_action( 'wp_import_insert_post', $post_id, $original_post_ID, $postdata, $post );
                }

                if ( is_wp_error( $post_id ) ) {
                    printf(
                        __( 'Failed to import %1$s &#8220;%2$s&#8221;', 'devmonsta' ),
                        $post_type_object->labels->singular_name,
                        esc_html( $post['post_title'] )
                    );

                    if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
                        echo ': ' . $post_id->get_error_message();
                    }

                    echo '<br />';
                    continue;
                }

                if ( $post['is_sticky'] == 1 ) {
                    stick_post( $post_id );
                }

            }

            // map pre-import ID to local ID
            $this->processed_posts[intval( $post['post_id'] )] = (int) $post_id;

            if ( !isset( $post['terms'] ) ) {
                $post['terms'] = [];
            }

            $post['terms'] = apply_filters( 'wp_import_post_terms', $post['terms'], $post_id, $post );

            // add categories, tags and other terms
            if ( !empty( $post['terms'] ) ) {
                $terms_to_set = [];
                foreach ( $post['terms'] as $term ) {
                    // back compat with WXR 1.0 map 'tag' to 'post_tag'
                    $taxonomy    = ( 'tag' == $term['domain'] ) ? 'post_tag' : $term['domain'];
                    $term_exists = term_exists( $term['slug'], $taxonomy );
                    $term_id     = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;

                    if ( !$term_id ) {
                        $t = wp_insert_term( $term['name'], $taxonomy, [ 'slug' => $term['slug'] ] );

                        if ( !is_wp_error( $t ) ) {
                            $term_id = $t['term_id'];
                            do_action( 'wp_import_insert_term', $t, $term, $post_id, $post );
                        } else {
                            printf( __( 'Failed to import %1$s %2$s', 'devmonsta' ), esc_html( $taxonomy ), esc_html( $term['name'] ) );

                            if ( defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
                                echo ': ' . esc_html( $t->get_error_message() );
                            }

                            echo '<br />';
                            do_action( 'wp_import_insert_term_failed', $t, $term, $post_id, $post );
                            continue;
                        }

                    }

                    $terms_to_set[$taxonomy][] = intval( $term_id );
                }

                foreach ( $terms_to_set as $tax => $ids ) {
                    $tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
                    do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $post );
                }

                unset( $post['terms'], $terms_to_set );
            }

            if ( !isset( $post['comments'] ) ) {
                $post['comments'] = [];
            }

            $post['comments'] = apply_filters( 'wp_import_post_comments', $post['comments'], $post_id, $post );

        // add/update comments
            if ( !empty( $post['comments'] ) ) {
                $num_comments      = 0;
                $inserted_comments = [];
                foreach ( $post['comments'] as $comment ) {
                    $comment_id                                       = $comment['comment_id'];
                    $newcomments[$comment_id]['comment_post_ID']      = $comment_post_ID;
                    $newcomments[$comment_id]['comment_author']       = $comment['comment_author'];
                    $newcomments[$comment_id]['comment_author_email'] = $comment['comment_author_email'];
                    $newcomments[$comment_id]['comment_author_IP']    = $comment['comment_author_IP'];
                    $newcomments[$comment_id]['comment_author_url']   = $comment['comment_author_url'];
                    $newcomments[$comment_id]['comment_date']         = $comment['comment_date'];
                    $newcomments[$comment_id]['comment_date_gmt']     = $comment['comment_date_gmt'];
                    $newcomments[$comment_id]['comment_content']      = $comment['comment_content'];
                    $newcomments[$comment_id]['comment_approved']     = $comment['comment_approved'];
                    $newcomments[$comment_id]['comment_type']         = $comment['comment_type'];
                    $newcomments[$comment_id]['comment_parent']       = $comment['comment_parent'];
                    $newcomments[$comment_id]['commentmeta']          = isset( $comment['commentmeta'] ) ? $comment['commentmeta'] : [];
                    if ( isset( $this->processed_authors[$comment['comment_user_id']] ) ) {
                        $newcomments[$comment_id]['user_id'] = $this->processed_authors[$comment['comment_user_id']];
                    }

                }

                ksort( $newcomments );

                foreach ( $newcomments as $key => $comment ) {

        // if this is a new post we can skip the comment_exists() check
                    if ( !$post_exists || !comment_exists( $comment['comment_author'], $comment['comment_date'] ) ) {
                        if ( isset( $inserted_comments[$comment['comment_parent']] ) ) {
                            $comment['comment_parent'] = $inserted_comments[$comment['comment_parent']];
                        }

                        $comment                 = wp_slash( $comment );
                        $comment                 = wp_filter_comment( $comment );
                        $inserted_comments[$key] = wp_insert_comment( $comment );
                        do_action( 'wp_import_insert_comment', $inserted_comments[$key], $comment, $comment_post_ID, $post );

                        foreach ( $comment['commentmeta'] as $meta ) {
                            $value = maybe_unserialize( $meta['value'] );
                            add_comment_meta( $inserted_comments[$key], $meta['key'], $value );
                        }

                        $num_comments++;
                    }

                }

                unset( $newcomments, $inserted_comments, $post['comments'] );
            }

            if ( !isset( $post['postmeta'] ) ) {
                $post['postmeta'] = [];
            }

            $post['postmeta'] = apply_filters( 'wp_import_post_meta', $post['postmeta'], $post_id, $post );

        // add/update post meta
            if ( !empty( $post['postmeta'] ) ) {
                foreach ( $post['postmeta'] as $meta ) {
                    $key   = apply_filters( 'import_post_meta_key', $meta['key'], $post_id, $post );
                    $value = false;

                    if ( '_edit_last' == $key ) {
                        if ( isset( $this->processed_authors[intval( $meta['value'] )] ) ) {
                            $value = $this->processed_authors[intval( $meta['value'] )];
                        } else {
                            $key = false;
                        }

                    }

                    // post meta
                    if ( $key ) {

                        // export gets meta straight from the DB so could have a serialized string
                        if ( !$value ) {
                            $value = maybe_unserialize( $meta['value'] );
                        }

                        if( $key == "_elementor_data" ){
                            $current_site_url      = get_site_url();
                            $current_site_url      = str_replace( '/', '\/', $current_site_url );

                            $demo_site_url = $this->base_site_url;
                            $demo_site_url = str_replace( '/', '\/', $demo_site_url );
                            
                            $value          = str_replace( $demo_site_url, $current_site_url, $value );
                        }

                        add_post_meta( $post_id, $key, wp_slash( $value ) );
                        do_action( 'import_post_meta', $post_id, $key,  wp_slash( $value ) );

                        // if the post has a featured image, take note of this in case of remap
                        if ( '_thumbnail_id' == $key ) {
                            $this->featured_images[$post_id] = (int) $value;
                        }

                    }

                }

            }

        }

        unset( $this->posts );
    }

    public function woocommerce_product_attributes_registration( $data ) {
        global $wpdb;

        if ( strstr( $data['taxonomy'], 'pa_' ) ) {
            if ( !taxonomy_exists( $data['taxonomy'] ) ) {
                $attribute_name = wc_sanitize_taxonomy_name( str_replace( 'pa_', '', $data['taxonomy'] ) );

        // Create the taxonomy
                if ( !in_array( $attribute_name, wc_get_attribute_taxonomies() ) ) {
                    $attribute = [
                        'attribute_label'   => $attribute_name,
                        'attribute_name'    => $attribute_name,
                        'attribute_type'    => 'select',
                        'attribute_orderby' => 'menu_order',
                        'attribute_public'  => 0,
                    ];
                    $wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute );
                    delete_transient( 'wc_attribute_taxonomies' );
                }

                // Register the taxonomy now so that the import works!
                register_taxonomy(
                    $data['taxonomy'],
                    apply_filters( 'woocommerce_taxonomy_objects_' . $data['taxonomy'], [ 'product' ] ),
                    apply_filters( 'woocommerce_taxonomy_args_' . $data['taxonomy'], [
                        'hierarchical' => true,
                        'show_ui'      => false,
                        'query_var'    => true,
                        'rewrite'      => false,
                    ] )
                );
            }

        }

        return $data;
    }

    function process_menu_item( $item ) {

        // skip draft, orphaned menu items
        if ( 'draft' == $item['status'] ) {
            return;
        }

        $menu_slug = false;
        if ( isset( $item['terms'] ) ) {

        // loop through terms, assume first nav_menu term is correct menu
            foreach ( $item['terms'] as $term ) {
                if ( 'nav_menu' == $term['domain'] ) {
                    $menu_slug = $term['slug'];
                    break;
                }

            }

        }

        // no nav_menu term associated with this menu item
        if ( !$menu_slug ) {
            _e( 'Menu item skipped due to missing menu slug', 'devmonsta' );
            echo '<br />';
            return;
        }

        $menu_id = term_exists( $menu_slug, 'nav_menu' );
        if ( !$menu_id ) {
            printf( __( 'Menu item skipped due to invalid menu slug: %s', 'devmonsta' ), esc_html( $menu_slug ) );
            echo '<br />';
            return;
        } else {
            $menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;
        }

        foreach ( $item['postmeta'] as $meta ) {
            if ( version_compare( PHP_VERSION, '7.0.0' ) >= 0 ) {
                ${$meta['key']}
                = $meta['value'];
            } else {
                $$meta['key'] = $meta['value'];
            }

        }

        if ( 'taxonomy' == $_menu_item_type && isset( $this->processed_terms[intval( $_menu_item_object_id )] ) ) {
            $_menu_item_object_id = $this->processed_terms[intval( $_menu_item_object_id )];
        } elseif ( 'post_type' == $_menu_item_type && isset( $this->processed_posts[intval( $_menu_item_object_id )] ) ) {
            $_menu_item_object_id = $this->processed_posts[intval( $_menu_item_object_id )];
        } elseif ( 'custom' != $_menu_item_type ) {
            // associated object is missing or not imported yet, we'll retry later
            $this->missing_menu_items[] = $item;
            return;
        }

        if ( isset( $this->processed_menu_items[intval( $_menu_item_menu_item_parent )] ) ) {
            $_menu_item_menu_item_parent = $this->processed_menu_items[intval( $_menu_item_menu_item_parent )];
        } elseif ( $_menu_item_menu_item_parent ) {
            $this->menu_item_orphans[intval( $item['post_id'] )] = (int) $_menu_item_menu_item_parent;
            $_menu_item_menu_item_parent                       = 0;
        }

        // wp_update_nav_menu_item expects CSS classes as a space separated string
        $_menu_item_classes = maybe_unserialize( $_menu_item_classes );

        if ( is_array( $_menu_item_classes ) ) {
            $_menu_item_classes = implode( ' ', $_menu_item_classes );
        }

        $args = [
            'menu-item-object-id'   => $_menu_item_object_id,
            'menu-item-object'      => $_menu_item_object,
            'menu-item-parent-id'   => $_menu_item_menu_item_parent,
            'menu-item-position'    => intval( $item['menu_order'] ),
            'menu-item-type'        => $_menu_item_type,
            'menu-item-title'       => $item['post_title'],
            'menu-item-url'         => $_menu_item_url,
            'menu-item-description' => $item['post_content'],
            'menu-item-attr-title'  => $item['post_excerpt'],
            'menu-item-target'      => $_menu_item_target,
            'menu-item-classes'     => $_menu_item_classes,
            'menu-item-xfn'         => $_menu_item_xfn,
            'menu-item-status'      => $item['status'],
        ];

        $id = wp_update_nav_menu_item( $menu_id, 0, $args );

        if ( $id && !is_wp_error( $id ) ) {
            $this->processed_menu_items[intval( $item['post_id'] )] = (int) $id;
        }

    }

    function process_attachment( $post, $url ) {

        if ( !$this->fetch_attachments ) {
            return new WP_Error(
                'attachment_processing_error',
                __( 'Fetching attachments is not enabled', 'devmonsta' )
            );
        }

        // if the URL is absolute, but does not contain address, then upload it assuming base_site_url
        if ( preg_match( '|^/[\w\W]+$|', $url ) ) {
            $url = rtrim( $this->base_url, '/' ) . $url;
        }

        $upload = $this->fetch_remote_file( $url, $post );
        if ( is_wp_error( $upload ) ) {
            return $upload;
        }

        if ( $info = wp_check_filetype( $upload['file'] ) ) {
            $post['post_mime_type'] = $info['type'];
        } else {
            return new WP_Error( 'attachment_processing_error', __( 'Invalid file type', 'devmonsta' ) );
        }

        $post['guid'] = $upload['url'];

        // as per wp-admin/includes/upload.php
        $post_id = wp_insert_attachment( $post, $upload['file'] );
        wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );

        // remap resized image URLs, works by stripping the extension and remapping the URL stub.
        if ( preg_match( '!^image/!', $info['type'] ) ) {
            $parts = pathinfo( $url );
            $name  = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

            $parts_new = pathinfo( $upload['url'] );
            $name_new  = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

            $this->url_remap[$parts['dirname'] . '/' . $name] = $parts_new['dirname'] . '/' . $name_new;
        }

        return $post_id;
    }

    function fetch_remote_file( $url, $post ) {
        // extract the file name and extension from the url
        $file_name = basename( $url );

        // get placeholder file in the upload dir with a unique, sanitized filename
        $upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );

        if ( $upload['error'] ) {
            return new WP_Error( 'upload_dir_error', $upload['error'] );
        }

        // fetch the remote url and write it to the placeholder file
        $remote_response = wp_safe_remote_get(
            $url,
            [
                'timeout'  => 300,
                'stream'   => true,
                'filename' => $upload['file'],
            ]
        );

        $headers = wp_remote_retrieve_headers( $remote_response );

        // request failed
        if ( !$headers ) {
            @unlink( $upload['file'] );
            return new WP_Error( 'import_file_error', __( 'Remote server did not respond', 'devmonsta' ) );
        }

        $remote_response_code = wp_remote_retrieve_response_code( $remote_response );

        // make sure the fetch was successful
        if ( $remote_response_code != '200' ) {
            @unlink( $upload['file'] );
            return new WP_Error( 'import_file_error', sprintf( __( 'Remote server returned error response %1$d %2$s', 'devmonsta' ), esc_html( $remote_response_code ), get_status_header_desc( $remote_response_code ) ) );
        }

        $filesize = filesize( $upload['file'] );

        if ( 0 == $filesize ) {
            @unlink( $upload['file'] );
            return new WP_Error( 'import_file_error', __( 'Zero size file downloaded', 'devmonsta' ) );
        }

        $max_size = (int) $this->max_attachment_size();

        if ( !empty( $max_size ) && $filesize > $max_size ) {
            @unlink( $upload['file'] );
            return new WP_Error( 'import_file_error', sprintf( __( 'Remote file is too large, limit is %s', 'devmonsta' ), size_format( $max_size ) ) );
        }

        // keep track of the old and new urls so we can substitute them later
        $this->url_remap[$url]          = $upload['url'];
        $this->url_remap[$post['guid']] = $upload['url'];
        // r13735, really needed?

        // keep track of the destination if the remote url is redirected somewhere else
        if ( isset( $headers['x-final-location'] ) && $headers['x-final-location'] != $url ) {
            $this->url_remap[$headers['x-final-location']] = $upload['url'];
        }

        return $upload;
    }

    function backfill_parents() {
        global $wpdb;

        // find parents for post orphans
        foreach ( $this->post_orphans as $child_id => $parent_id ) {
            $local_child_id = $local_parent_id = false;
            if ( isset( $this->processed_posts[$child_id] ) ) {
                $local_child_id = $this->processed_posts[$child_id];
            }

            if ( isset( $this->processed_posts[$parent_id] ) ) {
                $local_parent_id = $this->processed_posts[$parent_id];
            }

            if ( $local_child_id && $local_parent_id ) {
                $wpdb->update( $wpdb->posts, [ 'post_parent' => $local_parent_id ], [ 'ID' => $local_child_id ], '%d', '%d' );
                clean_post_cache( $local_child_id );
            }

        }

        // all other posts/terms are imported, retry menu items with missing associated object
        $missing_menu_items = $this->missing_menu_items;

        foreach ( $missing_menu_items as $item ) {
            $this->process_menu_item( $item );
        }

        // find parents for menu item orphans
        foreach ( $this->menu_item_orphans as $child_id => $parent_id ) {
            $local_child_id = $local_parent_id = 0;
            if ( isset( $this->processed_menu_items[$child_id] ) ) {
                $local_child_id = $this->processed_menu_items[$child_id];
            }

            if ( isset( $this->processed_menu_items[$parent_id] ) ) {
                $local_parent_id = $this->processed_menu_items[$parent_id];
            }

            if ( $local_child_id && $local_parent_id ) {
                update_post_meta( $local_child_id, '_menu_item_menu_item_parent', (int) $local_parent_id );
            }

        }

    }

    /**
     * Use stored mapping information to update old attachment URLs
     */
    function backfill_attachment_urls() {
        global $wpdb;
        // make sure we do the longest urls first, in case one is a substring of another
        uksort( $this->url_remap, [ &$this, 'cmpr_strlen' ] );

        foreach ( $this->url_remap as $from_url => $to_url ) {
            // remap urls in post_content
            $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url, $to_url ) );
            // remap enclosure urls
            $result = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key='enclosure'", $from_url, $to_url ) );
        }

    }

    /**
     * Update _thumbnail_id meta to new, imported attachment IDs
     */
    function remap_featured_images() {

        // cycle through posts that have a featured image
        foreach ( $this->featured_images as $post_id => $value ) {
            if ( isset( $this->processed_posts[$value] ) ) {
                $new_id = $this->processed_posts[$value];

        // only update if there's a difference
                if ( $new_id != $value ) {
                    update_post_meta( $post_id, '_thumbnail_id', $new_id );
                }

            }

        }

    }

    /**
     * Parse a WXR file
     *
     * @param string $file Path to WXR file for parsing
     * @return array Information gathered from the WXR file
     */
    function parse( $file ) {
        $parser = new Devm_WXR_Parser();
        return $parser->parse( $file );
    }

    // Close div.wrap
    function footer() {
        echo '</div>';
    }

    /**
     * Display introductory text and file upload form
     */
    function greet() {
        echo '<div class="narrow">';
        echo '<p>' . __( 'Howdy! Upload your WordPress eXtended RSS (WXR) file and we&#8217;ll import the posts, pages, comments, custom fields, categories, and tags into this site.', 'devmonsta' ) . '</p>';
        echo '<p>' . __( 'Choose a WXR (.xml) file to upload, then click Upload file and import.', 'devmonsta' ) . '</p>';
        wp_import_upload_form( 'admin.php?import=wordpress&amp;step=1' );
        echo '</div>';
    }

    /**
     * Decide if the given meta key maps to information we will want to import
     *
     * @param string $key The meta key to check
     * @return string|bool The key if we do want to import, false if not
     */
    function is_valid_meta_key( $key ) {

    // skip attachment metadata since we'll regenerate it from scratch

    // skip _edit_lock as not relevant for import
        if ( in_array( $key, [ '_wp_attached_file', '_wp_attachment_metadata', '_edit_lock' ] ) ) {
            return false;
        }

        return $key;
    }

    function allow_create_users() {
        return apply_filters( 'import_allow_create_users', true );
    }

    function allow_fetch_attachments() {
        return apply_filters( 'import_allow_fetch_attachments', true );
    }

    function max_attachment_size() {
        return apply_filters( 'import_attachment_size_limit', 0 );
    }

    /**
     * Added to http_request_timeout filter to force timeout at 60 seconds during import
     *
     * @return int 60
     */
    function bump_request_timeout( $val ) {
        return 60;
    }

    // return the difference in length between two strings
    function cmpr_strlen( $a, $b ) {
        return strlen( $b ) - strlen( $a );
    }

}


// $importer = new Devm_WXR_Importer();
// $importer->import_module('revslider', get_template_directory() . "/sliders/home-default/slider.zip" );