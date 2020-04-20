<?php if ( ! defined( 'DMS' ) ) {
	die( 'Forbidden' );
}

require_once dirname(__FILE__) . '/includes/class-dms-wp-editor-settings.php';

class DMS_Option_Type_Wp_Editor extends DMS_Option_Type {
	public function get_type() {
		return 'wp-editor';
	}

	protected function _get_data_for_js($id, $option, $data = array()) {
		return false;
	}

	/**
	 * @internal
	 */
	protected function _get_defaults() {
		return array(
			'value' => '',
			'size' => 'small', // small, large
			'editor_height' => 160,
			'wpautop' => true,
			'editor_type' => false, // tinymce, html

			/**
			 * By default, you don't have any shortcodes into the editor.
			 *
			 * You have two possible values:
			 *   - false:   You will not have a shortcodes button at all
			 *   - true:    the default values you provide in wp-shortcodes
			 *              extension filter will be used
			 *
			 *   - An array of shortcodes
			 */
			'shortcodes' => false // true, array('button', map')

			/**
			 * Also available
			 * https://github.com/WordPress/WordPress/blob/4.4.2/wp-includes/class-wp-editor.php#L80-L94
			 */
		);
	}

	protected function get_default_shortcodes_list() {
		

		return $editor_shortcodes->default_shortcodes_list();
	}

	protected function _init() {
		add_filter('tiny_mce_before_init', array($this, '_filter_disable_default_init'), 10, 2);
	}

	// used in js and html
	public function get_id_prefix() {
		return 'dms_wp_editor_';
	}

	/**
	 * @internal
	 */
	public function _filter_disable_default_init($mceInit, $editor_id){
		if (preg_match('/^'. preg_quote($this->get_id_prefix(), '/') .'/', $editor_id)) {
			$mceInit['wp_skip_init'] = true;
		}

		return $mceInit;
	}

	/**
	 * @internal
	 */
	protected function _render( $id, $option, $data ) {
	
		$editor_manager = new DMS_WP_Editor_Manager($id, $option, $data);
		return $editor_manager->get_html();
	}

	/**
	 * @internal
	 * {@inheritdoc}
	 */
	protected function _enqueue_static( $id, $option, $data ) {
		if ( ! wp_script_is( 'editor' ) ) {
			wp_enqueue_script( 'editor' );
			wp_enqueue_script( 'quicktags' );
			wp_enqueue_script('dms-ext-shortcodes-editor-integration');

			if ( ! class_exists( '_WP_Editors', false ) ) {
				require( ABSPATH . WPINC . '/class-wp-editor.php' );
			}

			add_action( 'admin_print_footer_scripts', '_WP_Editors::print_default_editor_scripts', 45 );
		}

		/**
		 * The below styles usually are included directly in html when wp_editor() is called
		 * but since we call it (below) wrapped in ob_start()...ob_end_clean() the html is not printed.
		 * So included the styles manually.
		 */

		wp_enqueue_style(
		/**
		 * https://github.com/WordPress/WordPress/blob/4.4.2/wp-includes/script-loader.php#L731
		 * without prefix it won't enqueue
		 */
			'dms-option-type-' . $this->get_type() . '-dashicons',
			includes_url( 'css/dashicons.min.css' ),
			array(),
			dms()->manifest->get_version()
		);

		wp_enqueue_style(
		/**
		 * https://github.com/WordPress/WordPress/blob/4.4.2/wp-includes/script-loader.php#L737
		 * without prefix it won't enqueue
		 */
			'dms-option-type-' . $this->get_type() . '-editor-buttons',
			includes_url( '/css/editor.min.css' ),
			array( 'dashicons', 'dms-unycon' ),
			dms()->manifest->get_version()
		);

		$uri = dms_get_framework_directory_uri(
			'/includes/option-types/' . $this->get_type() . '/static'
		);

		wp_enqueue_script(
			'dms-option-type-' . $this->get_type(),
			$uri . '/scripts.js',
			array( 'jquery', 'dms-events', 'editor', 'dms' ),
			dms()->manifest->get_version(),
			true
		);

		wp_enqueue_style(
			'dms-option-type-' . $this->get_type(),
			$uri . '/styles.css',
			array( 'dashicons', 'editor-buttons' ),
			dms()->manifest->get_version()
		);

		do_action( 'dms:option-type:wp-editor:enqueue-scripts' );
	}

	/**
	 * @internal
	 */
	protected function _get_value_from_input( $option, $input_value ) {
		if ( is_null( $input_value ) ) {
			return $option['value'];
		}

		$value = (string) $input_value;

		if ( isset($option['wpautop']) && $option['wpautop'] === true ) {
			$value = preg_replace( "/\n/i", '', wpautop( $value ) );
		}

		return $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function _get_backend_width_type() {
		return 'auto';
	}
}
