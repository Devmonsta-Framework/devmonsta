<?php if ( ! defined( 'DMS' ) ) {
	die( 'Forbidden' );
}
/**
 * Filters and Actions
 */

/**
 * Option types
 */
{
	/**
	 * @internal
	 */
	function _action_dms_init_option_types() {
		DMS_Option_Type::register( 'DMS_Option_Type_Hidden' );
		DMS_Option_Type::register( 'DMS_Option_Type_Text' );
		DMS_Option_Type::register( 'DMS_Option_Type_Short_Text' );
		DMS_Option_Type::register( 'DMS_Option_Type_Password' );
		DMS_Option_Type::register( 'DMS_Option_Type_Textarea' );
		DMS_Option_Type::register( 'DMS_Option_Type_Html' );
		DMS_Option_Type::register( 'DMS_Option_Type_Html_Fixed' );
		DMS_Option_Type::register( 'DMS_Option_Type_Html_Full' );
		DMS_Option_Type::register( 'DMS_Option_Type_Checkbox' );
		DMS_Option_Type::register( 'DMS_Option_Type_Checkboxes' );
		DMS_Option_Type::register( 'DMS_Option_Type_Radio' );
		DMS_Option_Type::register( 'DMS_Option_Type_Select' );
		DMS_Option_Type::register( 'DMS_Option_Type_Short_Select' );
		DMS_Option_Type::register( 'DMS_Option_Type_Select_Multiple' );
		DMS_Option_Type::register( 'DMS_Option_Type_Unique' );
		DMS_Option_Type::register( 'DMS_Option_Type_GMap_Key' );
		DMS_Option_Type::register( 'DMS_Option_Type_Addable_Box' );
		DMS_Option_Type::register( 'DMS_Option_Type_Addable_Option' );
		DMS_Option_Type::register( 'DMS_Option_Type_Addable_Popup' );
		DMS_Option_Type::register( 'DMS_Option_Type_Addable_Popup_Full' );
		DMS_Option_Type::register( 'DMS_Option_Type_Background_Image' );
		DMS_Option_Type::register( 'DMS_Option_Type_Color_Picker' );
		DMS_Option_Type::register( 'DMS_Option_Type_Date_Picker' );
		DMS_Option_Type::register( 'DMS_Option_Type_Datetime_Picker' );
		DMS_Option_Type::register( 'DMS_Option_Type_Datetime_Range' );
		DMS_Option_Type::register( 'DMS_Option_Type_Gradient' );
		DMS_Option_Type::register( 'DMS_Option_Type_Icon' );
		DMS_Option_Type::register( 'DMS_Option_Type_Image_Picker' );
		DMS_Option_Type::register( 'DMS_Option_Type_Map' );
		DMS_Option_Type::register( 'DMS_Option_Type_Multi' );
		DMS_Option_Type::register( 'DMS_Option_Type_Multi_Picker' );
		DMS_Option_Type::register( 'DMS_Option_Type_Multi_Upload' );
		DMS_Option_Type::register( 'DMS_Option_Type_Popup' );
		DMS_Option_Type::register( 'DMS_Option_Type_Radio_Text' );
		DMS_Option_Type::register( 'DMS_Option_Type_Range_Slider' );
		DMS_Option_Type::register( 'DMS_Option_Type_Rgba_Color_Picker' );
		DMS_Option_Type::register( 'DMS_Option_Type_Slider' );
		DMS_Option_Type::register( 'DMS_Option_Type_Slider_Short' );
		DMS_Option_Type::register( 'DMS_Option_Type_Switch' );
		DMS_Option_Type::register( 'DMS_Option_Type_Typography' );
		DMS_Option_Type::register( 'DMS_Option_Type_Typography_v2' );
		DMS_Option_Type::register( 'DMS_Option_Type_Upload' );
		DMS_Option_Type::register( 'DMS_Option_Type_Wp_Editor' );

		{
			$favorites = new DMS_Icon_V2_Favorites_Manager();
			$favorites->attach_ajax_actions();

			DMS_Option_Type::register( 'DMS_Option_Type_Icon_v2' );
		}

		{
			DMS_Option_Type::register( 'DMS_Option_Type_Multi_Select' );
		}

		{
			DMS_Option_Type::register( 'DMS_Option_Type_Oembed' );
		}
	}

	add_action( 'dms_option_types_init', '_action_dms_init_option_types' );

	/**
	 * Some option-types have add_action('wp_ajax_...')
	 * so init all option-types if current request is ajax
	 * @since 2.6.1
	 */
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		function _action_dms_init_option_types_on_ajax() {
			foreach (dms()->backend->get_option_types() as $type) {
				dms()->backend->option_type($type);
			}
		}

		add_action( 'dms_init', '_action_dms_init_option_types_on_ajax' );
	}

	/**
	 * Prevent Fatal Error if someone is registering option-types in old way (right away)
	 * not in 'dms_option_types_init' action
	 *
	 * @param string $class
	 */
	function _dms_autoload_option_types( $class ) {
		if ( 'DMS_Option_Type' === $class ) {
			if ( is_admin() && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				DMS_Flash_Messages::add(
					'option-type-register-wrong',
					__( "Please register option-types on 'dms_option_types_init' action", 'dms' ),
					'warning'
				);
			}
		} elseif ( 'DMS_Container_Type' === $class ) {
			if ( is_admin() && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				DMS_Flash_Messages::add(
					'container-type-register-wrong',
					__( "Please register container-types on 'dms_container_types_init' action", 'dms' ),
					'warning'
				);
			}
		}
	}

	spl_autoload_register( '_dms_autoload_option_types' );
}

/**
 * Container types
 */
{
	/**
	 * @internal
	 */
	function _action_dms_init_container_types() {
		DMS_Container_Type::register( 'DMS_Container_Type_Group' );
		DMS_Container_Type::register( 'DMS_Container_Type_Box' );
		DMS_Container_Type::register( 'DMS_Container_Type_Popup' );
		DMS_Container_Type::register( 'DMS_Container_Type_Tab' );
	}

	add_action( 'dms_container_types_init', '_action_dms_init_container_types' );
}

function _dms_filter_github_api_url( $url ) {
	return 'https://github-api-cache.unyson.io';
}

add_filter( 'dms_github_api_url', '_dms_filter_github_api_url' );

{
	add_action( 'wp_tiny_mce_init', '_dms_action_tiny_mce_init' );
	function _dms_action_tiny_mce_init( $mce_settings ) {
		?>
		<script type="text/javascript">
			if (typeof dmsEvents != 'undefined') {
				dmsEvents.trigger('dms:tinymce:init:before');
			}
		</script>
		<?php
	}

	add_action( 'after_wp_tiny_mce', '_dms_action_after_wp_tiny_mce' );
	function _dms_action_after_wp_tiny_mce( $mce_settings ) {
		?>
		<script type="text/javascript">
			if (typeof dmsEvents != 'undefined') {
				dmsEvents.trigger('dms:tinymce:init:after');
			}
		</script>
		<?php
	}
}

// DMS_Form hooks
{
	if ( is_admin() ) {
		/**
		 * Display form errors in admin side
		 * @internal
		 */
		function _action_dms_form_show_errors_in_admin() {
			$form = DMS_Form::get_submitted();

			if ( ! $form || $form->is_valid() ) {
				return;
			}

			foreach ( $form->get_errors() as $input_name => $error_message ) {
				DMS_Flash_Messages::add( 'dms-form-admin-' . $input_name, $error_message, 'error' );
			}
		}

		add_action( 'wp_loaded', '_action_dms_form_show_errors_in_admin', 111 );
	} else {
		
		function _action_dms_form_frontend_default_styles() {
			$form = DMS_Form::get_submitted();

			if ( ! $form || $form->is_valid() ) {
				return;
			}

			echo '<style type="text/css">.dms-form-errors { color: #bf0000; }</style>';
		}

		add_action( 'wp_print_styles', '_action_dms_form_frontend_default_styles' );
	}
}

// DMS_Flash_Messages hooks
{
	if ( is_admin() ) {
		/**
		 * Start the session before the content is sent to prevent the "headers already sent" warning
		 * @internal
		 */
		function _action_dms_flash_message_backend_prepare() {
			if ( apply_filters( 'dms_use_sessions', true ) && ! session_id()  ) {
				session_start();
			}
		}

		add_action( 'current_screen', '_action_dms_flash_message_backend_prepare', 9999 );

		/**
		 * Display flash messages in backend as notices
		 */
		add_action( 'admin_notices', array( 'DMS_Flash_Messages', '_print_backend' ) );
	} else {
		/**
		 * Start the session before the content is sent to prevent the "headers already sent" warning
		 * @internal
		 */
		function _action_dms_flash_message_frontend_prepare() {
			if (
			    apply_filters( 'dms_use_sessions', true )
                &&
				/**
				 * In ajax it's not possible to call flash message after headers were sent,
				 * so there will be no "headers already sent" warning.
				 * Also in the Backups extension, are made many internal ajax request,
				 * each creating a new independent request that don't remember/use session cookie from previous request,
				 * thus on server side are created many (not used) new sessions.
				 */
				! ( defined( 'DOING_AJAX' ) && DOING_AJAX )
				&&
				! session_id()
			) {
				session_start();
			}
		}

		add_action( 'send_headers', '_action_dms_flash_message_frontend_prepare', 9999 );

		/**
		 * Print flash messages in frontend if this has not been done from theme
		 */
		function _action_dms_flash_message_frontend_print() {
			if ( DMS_Flash_Messages::_frontend_printed() ) {
				return;
			}

			if ( ! DMS_Flash_Messages::_print_frontend() ) {
				return;
			}

			?>
			<script type="text/javascript">
				(function () {
					if (typeof jQuery === "undefined") {
						return;
					}

					jQuery(function ($) {
						var $container;

						// Try to find the content element
						{
							var selector, selectors = [
								'#main #content',
								'#content #main',
								'#main',
								'#content',
								'#content-container',
								'#container',
								'.container:first'
							];

							while (selector = selectors.shift()) {
								$container = $(selector);

								if ($container.length) {
									break;
								}
							}
						}

						if (!$container.length) {
							// Try to find main page H1 container
							$container = $('h1:first').parent();
						}

						if (!$container.length) {
							// If nothing found, just add to body
							$container = $(document.body);
						}

						$(".dms-flash-messages").prependTo($container);
					});
				})();
			</script>
			<style type="text/css">
				.dms-flash-messages .dms-flash-type-error {
					color: #f00;
				}

				.dms-flash-messages .dms-flash-type-warning {
					color: #f70;
				}

				.dms-flash-messages .dms-flash-type-success {
					color: #070;
				}

				.dms-flash-messages .dms-flash-type-info {
					color: #07f;
				}
			</style>
			<?php
		}

		add_action( 'wp_footer', '_action_dms_flash_message_frontend_print', 9999 );
	}
}

// DMS_Resize hooks
{
	if ( ! function_exists( 'dms_delete_resized_thumbnails' ) ) {
		function dms_delete_resized_thumbnails( $id ) {
			$images = wp_get_attachment_metadata( $id );
			if ( ! empty( $images['resizes'] ) ) {
				$uploads_dir = wp_upload_dir();
				foreach ( $images['resizes'] as $image ) {
					$file = $uploads_dir['basedir'] . '/' . $image;
					@unlink( $file );
				}
			}
		}

		add_action( 'delete_attachment', 'dms_delete_resized_thumbnails' );
	}
}

//WPML Hooks
{
	if ( is_admin() ) {
		add_action( 'icl_save_term_translation', '_dms_action_wpml_duplicate_term_options', 20, 2 );
		function _dms_action_wpml_duplicate_term_options( $original, $translated ) {
			$original_options = dms_get_db_term_option(
				dms_akg( 'term_id', $original ),
				dms_akg( 'taxonomy', $original )
			);

			if ( $original_options !== null ) {
				dms_set_db_term_option(
					dms_akg( 'term_id', $translated ),
					dms_akg( 'taxonomy', $original ),
					null,
					$original_options
				);
			}
		}
	}
}