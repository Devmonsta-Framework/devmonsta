<?php if (!defined('DMS')) die('Forbidden');

/**
 * Used in dms()->backend
 * @internal
 */
class DMS_Settings_Form_Theme extends DMS_Settings_Form {
	protected function _init() {
		$this
			->set_is_ajax_submit( dms()->theme->get_config('settings_form_ajax_submit') )
			->set_is_side_tabs( dms()->theme->get_config('settings_form_side_tabs') )
			->set_string( 'title', __('Theme Settings', 'dms') );

		{
			add_action('admin_init', array($this, '_action_get_title_from_menu'));
			add_action('admin_menu', array($this, '_action_admin_menu'));
			add_action('admin_enqueue_scripts', array($this, '_action_admin_enqueue_scripts'),
				
				11
			);
		}
	}

	public function get_options() {
		
		return dms()->theme->get_settings_options();
	}

	public function set_values($values) {
		dms_set_db_settings_option(null, $values);

		return $this;
	}

	public function get_values() {
		return dms_get_db_settings_option();
	}

	
	public function _action_get_title_from_menu() {
		if ($this->get_is_side_tabs()) {
			$title = dms()->theme->manifest->get_name();

			if (dms()->theme->manifest->get('author')) {
				if (dms()->theme->manifest->get('author_uri')) {
					$title .= ' '. dms_html_tag('a', array(
							'href' => dms()->theme->manifest->get('author_uri'),
							'target' => '_blank'
						), '<small>' . __('by', 'dms') . ' ' . dms()->theme->manifest->get('author') . '</small>');
				} else {
					$title .= ' <small>' . dms()->theme->manifest->get('author') . '</small>';
				}
			}

			$this->set_string('title', $title);
		} else {
			// Extract page title from menu title
			do {
				global $menu, $submenu;

				if (is_array($menu)) {
					foreach ($menu as $_menu) {
						if ($_menu[2] === dms()->backend->_get_settings_page_slug()) {
							$title = $_menu[0];
							break 2;
						}
					}
				}

				if (is_array($submenu)) {
					foreach ($submenu as $_menu) {
						foreach ($_menu as $_submenu) {
							if ($_submenu[2] === dms()->backend->_get_settings_page_slug()) {
								$title = $_submenu[0];
								break 3;
							}
						}
					}
				}
			} while(false);

			if (isset($title)) {
				$this->set_string('title', $title);
			}
		}
	}

	/**
	 * @internal
	 */
	public function _action_admin_menu() {
		$data = array(
			'capability'       => 'manage_options',
			'slug'             => dms()->backend->_get_settings_page_slug(),
			'content_callback' => array( $this, 'render' ),
		);

		if ( ! current_user_can( $data['capability'] ) ) {
			return;
		}

		if (dms()->theme->get_config('disable_theme_settings_page', false)) {
			return;
		}

		if ( ! dms()->theme->locate_path('/options/settings.php') ) {
			return;
		}
	
		{
			global $_registered_pages;

			$found_hooknames = array();

			if ( ! empty( $_registered_pages ) ) {
				foreach ( $_registered_pages as $hookname => $b ) {
					if ( strpos( $hookname, $data['slug'] ) !== false ) {
						$found_hooknames[ $hookname ] = true;
					}
				}
			}
		}
		
		do_action( 'dms_backend_add_custom_settings_menu', $data );
		
		{
			$menu_exists = false;

			if ( ! empty( $_registered_pages ) ) {
				foreach ( $_registered_pages as $hookname => $b ) {
					if ( isset( $found_hooknames[ $hookname ] ) ) {
						continue;
					}

					if ( strpos( $hookname, $data['slug'] ) !== false ) {
						$menu_exists = true;
						break;
					}
				}
			}
		}

		if ( $menu_exists ) {
			return;
		}

		add_theme_page(
			__( 'Theme Settings', 'dms' ),
			__( 'Theme Settings', 'dms' ),
			$data['capability'],
			$data['slug'],
			$data['content_callback']
		);

		add_action( 'admin_menu', array( $this, '_action_admin_change_theme_settings_order' ), 9999 );
	}

	/**
	 * @internal
	 */
	public function _action_admin_change_theme_settings_order() {
		global $submenu;

		if ( ! isset( $submenu['themes.php'] ) ) {
			// probably current user doesn't have this item in menu
			return;
		}

		$id    = dms()->backend->_get_settings_page_slug();
		$index = null;

		foreach ( $submenu['themes.php'] as $key => $sm ) {
			if ( $sm[2] == $id ) {
				$index = $key;
				break;
			}
		}

		if ( ! empty( $index ) ) {
			$item = $submenu['themes.php'][ $index ];
			unset( $submenu['themes.php'][ $index ] );
			array_unshift( $submenu['themes.php'], $item );
		}
	}

	/**
	 * @internal
	 */
	public function _action_admin_enqueue_scripts()
	{
		global $plugin_page;
		
		{
			if (dms()->backend->_get_settings_page_slug() === $plugin_page) {
				$this->enqueue_static();

				do_action('dms_admin_enqueue_scripts:settings');
			}
		}
	}
}
