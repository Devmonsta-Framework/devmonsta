<?php if (!defined('DM')) die('Forbidden');

/**
 * Used in dm()->backend
 * @internal
 */
class DM_Settings_Form_Theme extends DM_Settings_Form {
	protected function _init() {
		$this
			->set_is_ajax_submit( dm()->theme->get_config('settings_form_ajax_submit') )
			->set_is_side_tabs( dm()->theme->get_config('settings_form_side_tabs') )
			->set_string( 'title', __('Theme Settings', 'dm') );

		{
			add_action('admin_init', array($this, '_action_get_title_from_menu'));
			add_action('admin_menu', array($this, '_action_admin_menu'));
			add_action('admin_enqueue_scripts', array($this, '_action_admin_enqueue_scripts'),
				
				11
			);
		}
	}

	public function get_options() {
		
		return dm()->theme->get_settings_options();
	}

	public function set_values($values) {
		dm_set_db_settings_option(null, $values);

		return $this;
	}

	public function get_values() {
		return dm_get_db_settings_option();
	}

	
	public function _action_get_title_from_menu() {
		if ($this->get_is_side_tabs()) {
			$title = dm()->theme->manifest->get_name();

			if (dm()->theme->manifest->get('author')) {
				if (dm()->theme->manifest->get('author_uri')) {
					$title .= ' '. dm_html_tag('a', array(
							'href' => dm()->theme->manifest->get('author_uri'),
							'target' => '_blank'
						), '<small>' . __('by', 'dm') . ' ' . dm()->theme->manifest->get('author') . '</small>');
				} else {
					$title .= ' <small>' . dm()->theme->manifest->get('author') . '</small>';
				}
			}

			$this->set_string('title', $title);
		} else {
			// Extract page title from menu title
			do {
				global $menu, $submenu;

				if (is_array($menu)) {
					foreach ($menu as $_menu) {
						if ($_menu[2] === dm()->backend->_get_settings_page_slug()) {
							$title = $_menu[0];
							break 2;
						}
					}
				}

				if (is_array($submenu)) {
					foreach ($submenu as $_menu) {
						foreach ($_menu as $_submenu) {
							if ($_submenu[2] === dm()->backend->_get_settings_page_slug()) {
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
			'slug'             => dm()->backend->_get_settings_page_slug(),
			'content_callback' => array( $this, 'render' ),
		);

		if ( ! current_user_can( $data['capability'] ) ) {
			return;
		}

		if (dm()->theme->get_config('disable_theme_settings_page', false)) {
			return;
		}

		if ( ! dm()->theme->locate_path('/options/settings.php') ) {
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
		
		do_action( 'dm_backend_add_custom_settings_menu', $data );
		
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
			__( 'Theme Settings', 'dm' ),
			__( 'Theme Settings', 'dm' ),
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

		$id    = dm()->backend->_get_settings_page_slug();
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
			if (dm()->backend->_get_settings_page_slug() === $plugin_page) {
				$this->enqueue_static();

				do_action('dm_admin_enqueue_scripts:settings');
			}
		}
	}
}
