<?php defined( 'dm' ) or die();

/**
 * Theme Component
 * Works with framework customizations / theme directory
 */
final class _dm_Component_Theme {
	private static $cache_key = 'dm_theme';

	/**
	 * @var dm_Theme_Manifest
	 */
	public $manifest;

	public function __construct() {
		$manifest = array();

		if ( ( $manifest_file = apply_filters('dm_framework_manifest_path', dm_get_template_customizations_directory( '/theme/manifest.php' )) ) && is_file( $manifest_file ) ) {
			@include $manifest_file;
		}

		if ( is_child_theme() && ( $manifest_file = dm_get_stylesheet_customizations_directory( '/theme/manifest.php' ) ) && is_file( $manifest_file ) ) {
			$extracted = dm_get_variables_from_file( $manifest_file, array( 'manifest' => array() ) );
			if ( isset( $extracted['manifest'] ) ) {
				$manifest = array_merge( $manifest, $extracted['manifest'] );
			}
		}

		$this->manifest = new DM_Theme_Manifest( $manifest );
	}

	/**
	 * @internal
	 */
	public function _init() {
		add_action( 'admin_notices', array( $this, '_action_admin_notices' ) );
		
	}

	/**
	 * @internal
	 */
	public function _after_components_init() {
	}
	
	public function locate_path( $rel_path ) {
		if ( is_child_theme() && file_exists( dms_get_stylesheet_customizations_directory( '/theme' . $rel_path ) ) ) {
			return dm_get_stylesheet_customizations_directory( '/theme' . $rel_path );
		} elseif ( file_exists( dm_get_template_customizations_directory( '/theme' . $rel_path ) ) ) {
			return dm_get_template_customizations_directory( '/theme' . $rel_path );
		} else {
			return false;
		}
	}
	
	
	public function get_options( $name, array $variables = array() ) {
		$path = $this->locate_path( '/options/' . $name . '.php' );

		if ( ! $path ) {
			return array();
		}

		$variables = dm_get_variables_from_file( $path, array( 'options' => array() ), $variables );

		return $variables['options'];
	}

	public function get_settings_options() {
		$cache_key = self::$cache_key . '/options/settings';

		try {
			return DM_Cache::get( $cache_key );
		} catch ( DM_Cache_Not_Found_Exception $e ) {
			$options = apply_filters( 'dm_settings_options', $this->get_options( 'settings' ) );

			DM_Cache::set( $cache_key, $options );

			return $options;
		}
	}

	public function get_customizer_options() {
		$cache_key = self::$cache_key . '/options/customizer';

		try {
			return DM_Cache::get( $cache_key );
		} catch ( DM_Cache_Not_Found_Exception $e ) {
			$options = apply_filters( 'dm_customizer_options', $this->get_options( 'customizer' ) );

			DM_Cache::set( $cache_key, $options );

			return $options;
		}
	}

	public function get_post_options( $post_type ) {
		$cache_key = self::$cache_key . '/options/posts/' . $post_type;

		try {
			return DM_Cache::get( $cache_key );
		} catch ( DM_Cache_Not_Found_Exception $e ) {
			$options = apply_filters(
				'dm_post_options',
				apply_filters( "dm_post_options:$post_type", $this->get_options( 'posts/' . $post_type ) ),
				$post_type
			);

			DM_Cache::set( $cache_key, $options );

			return $options;
		}
	}

	public function get_taxonomy_options( $taxonomy ) {
		$cache_key = self::$cache_key . '/options/taxonomies/' . $taxonomy;

		try {
			return DM_Cache::get( $cache_key );
		} catch ( DM_Cache_Not_Found_Exception $e ) {
			$options = apply_filters(
				'dm_taxonomy_options',
				apply_filters( "dm_taxonomy_options:$taxonomy", $this->get_options( 'taxonomies/' . $taxonomy ) ),
				$taxonomy
			);

			DM_Cache::set( $cache_key, $options );

			return $options;
		}
	}


	final public function get_config( $key = null, $default_value = null ) {
		$cache_key = self::$cache_key . '/config';

		try {
			$config = DM_Cache::get( $cache_key );
		} catch ( DM_Cache_Not_Found_Exception $e ) {
			// default values
			$config = array(
				/** Toggle Theme Settings form ajax submit */
				'settings_form_ajax_submit' => true,
				/** Toggle Theme Settings side tabs */
				'settings_form_side_tabs'   => false,
				/** Toggle Tabs rendered all at once, or initialized only on open/display */
				'lazy_tabs'                 => true,
			);

			if ( file_exists( dm_get_template_customizations_directory( '/theme/config.php' ) ) ) {
				$variables = dm_get_variables_from_file( dm_get_template_customizations_directory( '/theme/config.php' ), array( 'cfg' => null ) );

				if ( ! empty( $variables['cfg'] ) ) {
					$config = array_merge( $config, $variables['cfg'] );
					unset( $variables );
				}
			}

			if ( is_child_theme() && file_exists( dm_get_stylesheet_customizations_directory( '/theme/config.php' ) ) ) {
				$variables = dm_get_variables_from_file( dm_get_stylesheet_customizations_directory( '/theme/config.php' ), array( 'cfg' => null ) );

				if ( ! empty( $variables['cfg'] ) ) {
					$config = array_merge( $config, $variables['cfg'] );
					unset( $variables );
				}
			}

			unset( $path );

			DM_Cache::set( $cache_key, $config );
		}

		return $key === null ? $config : dm_akg( $key, $config, $default_value );
	}

	/**
	 * @internal
	 */
	public function _action_admin_notices() {

		if ( is_admin() && ! dm()->theme->manifest->check_requirements() && current_user_can( 'manage_options' ) ) {
			echo
				'<div class="notice notice-warning">
					<p>' .
			            __( 'Theme requirements not met:', 'dm' ) . ' ' . dm()->theme->manifest->get_not_met_requirement_text() .
					'</p>
				</div>';
		}

		
		
	
	}

}
