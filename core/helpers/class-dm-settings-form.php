<?php if (!defined('DMS')) die('Forbidden');

/**
 * Helps you create settings forms
 * @since 2.6.9
 */
abstract class DM_Settings_Form {
	/**
	 * @return array
	 */
	abstract public function get_options();

	/**
	 * @return array
	 */
	abstract public function get_values();

	/**
	 * @param array|callable $values
	 * @return $this
	 */
	abstract public function set_values($values);

	/**
	 * Make sure all instances have unique id
	 * @var array
	 */
	private static $ids = array();

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var bool
	 */
	private $is_side_tabs = false;

	/**
	 * @var bool
	 */
	private $is_ajax_submit = false;

	/**
	 * @var DMS_Form
	 */
	private $dms_form;

	/**
	 * Translated text ( initialized in __construct() )
	 * @var array
	 */
	private $strings;

	private static $input_name_reset = '_dms_reset_options';
	private static $input_name_save = '_dms_save_options';

	final public function __construct($id) {
		if (isset(self::$ids[$id])) {
			trigger_error(__CLASS__ .' with id "'. $id .'" was already defined', E_USER_ERROR);
		} else {
			self::$ids[$id] = true;
		}

		$this->id = $id;
		$this->dms_form = new DMS_Form('dms-settings-form:'. $this->get_id(), array(
			'render' => array($this, '_form_render'),
			'validate' => array($this, '_form_validate'),
			'save' => array($this, '_form_save'),
		));
		$this->strings = array(
			'title' => __('Settings', 'dms'),
			'save_button' => __('Save Changes', 'dms'),
			'reset_button' => __('Reset Options', 'dms'),
			'reset_warning' => __("Click OK to reset.\nAll settings will be lost and replaced with default settings!", 'dms'),
		);

		$this->_init();
	}

	protected function _init() {}

	/**
	 * @return string
	 */
	final public function get_id() {
		return $this->id;
	}

	/**
	 * @return bool
	 */
	final public function get_is_ajax_submit() {
		return $this->is_ajax_submit;
	}

	/**
	 * In order for this to work, you must call $this->enqueue_static() on `admin_enqueue_scripts` action
	 * @param bool $is_ajax_submit
	 * @return $this
	 */
	final public function set_is_ajax_submit($is_ajax_submit) {
		$this->is_ajax_submit = (bool)$is_ajax_submit;

		return $this;
	}

	/**
	 * @return bool
	 */
	final public function get_is_side_tabs() {
		return $this->is_side_tabs;
	}

	/**
	 * @param bool $is_side_tabs
	 * @return $this
	 */
	final public function set_is_side_tabs($is_side_tabs) {
		$this->is_side_tabs = (bool)$is_side_tabs;

		return $this;
	}

	/**
	 * @param string $id
	 * @return string
	 */
	final public function get_string($id) {
		return isset($this->strings[$id]) ? $this->strings[$id] : null;
	}

	/**
	 * @param string $id
	 * @param string $value
	 * @return bool
	 */
	final public function set_string($id, $value) {
		if (isset($this->strings[$id])) {
			$this->strings[$id] = $value;
		}

		return $this;
	}

	public function form_capability() {
		return 'manage_options';
	}

	public function enqueue_static() {
		dms()->backend->enqueue_options_static($this->get_options());

		if ($this->get_is_ajax_submit()) {
			wp_enqueue_script('dms-form-helpers');
		}
	}

	final public function render() {
		echo '<div class="wrap">';

		if ( $this->get_is_side_tabs() ) {
			// this is needed for flash messages (admin notices) to be displayed properly
			echo '<h2 class="dms-hidden"></h2>';
		} else {
			echo '<h2>'. esc_html( $this->get_string('title') ) .'</h2><br/>';
		}

		$this->dms_form->render();

		echo '</div>';

		{
			remove_action( // In case render is called multiple times
				'admin_print_footer_scripts',
				array($this, '_action_admin_print_footer_scripts')
			);
			add_action(
				'admin_print_footer_scripts',
				array($this, '_action_admin_print_footer_scripts'),
				20
			);
		}
	}

	/**
	 * Previously the functionality from this class was hardcoded in dms()->backend for Theme Settings
	 * and there were hooks that developers use now, so we should use old hooks for Theme Settings form
	 * Backwards Compatibility
	 * @return bool
	 */
	private function is_theme_settings() {
		return $this->get_id() === 'theme-settings';
	}

	/**
	 * @param array $data
	 * @return array
	 * @internal
	 */
	public function _form_render($data) {
		$options = $this->get_options();

		if ( empty( $options ) ) {
			echo '<p><em>', esc_html__('No options to display.', 'dms'), '</em></p>';
			return $data;
		}

		if ($this->is_theme_settings()) {
			do_action('dms_settings_form_render', array(
				'ajax_submit' => $this->get_is_ajax_submit(),
				'side_tabs' => $this->get_is_side_tabs()
			));

			{
				$texts = apply_filters('dms_settings_form_texts', array(
					'save_button' => __('Save Changes', 'dms'),
					'reset_button' => __('Reset Options', 'dms'),
				));

				$this->set_string('save_button', $texts['save_button']);
				$this->set_string('reset_button', $texts['reset_button']);
			}
		}

		{
			$data['attr']['class'] = 'dms-settings-form';

			if ( $this->get_is_side_tabs() ) {
				$data['attr']['class'] .= ' dms-backend-side-tabs';
			}
		}

		$data['submit']['html'] = '<!-- -->'; // it's generated in view

		dms_render_view( dms_get_framework_directory( '/views/backend-settings-form.php' ), array(
			'form' => $this,
			'values' => (
				($values = DMS_Request::POST( dms()->backend->get_options_name_attr_prefix() ))
				// This is form submit, extract values from $_POST
				? ($values = dms_get_options_values_from_input( $options, $values ))
				// Use saved values
				: ($values = $this->get_values())
			),
			'is_theme_settings' => $this->is_theme_settings(),
			'input_name_reset' => self::$input_name_reset,
			'input_name_save' => self::$input_name_save,
			'js_form_selector' => 'form[data-dms-form-id="'. esc_js($this->dms_form->get_id()) .'"]',
		), false );

		return $data;
	}

	/**
	 * @param array $errors
	 * @return array
	 * @internal
	 */
	public function _form_validate( $errors ) {
		if ( ! current_user_can($this->form_capability()) ) {
			$errors['_no_permission'] = __( 'You have no permissions to change settings options', 'dms' );
		}

		return $errors;
	}

	/**
	 * @param array $data
	 * @return array
	 * @internal
	 */
	public function _form_save( $data ) {
		$flash_id   = 'dms-settings-form:save:'. $this->get_id();
		$old_values = (array)$this->get_values();

		if ( ! empty( $_POST[ self::$input_name_reset ] ) ) { // The "Reset" button was pressed
			/**
			 * Some values that don't relate to design, like API credentials, are useful to not be wiped out.
			 *
			 * Usage:
			 *
			 * add_filter('dms_settings_form_reset:values', '_filter_add_persisted_option', 10, 2);
			 * function _filter_add_persisted_option ($current_persisted, $old_values) {
			 *   $value_to_persist = dms_akg('my/multi/key', $old_values);
			 *   dms_aks('my/multi/key', $value_to_persist, $current_persisted);
			 *
			 *   return $current_persisted;
			 * }
			 */
			$new_values = $this->is_theme_settings()
				? apply_filters( 'dms_settings_form_reset:values', array(), $old_values )
				: apply_filters( 'dms:settings-form:' . $this->get_id() . ':reset:values', array(), $old_values );

			$this->set_values( $new_values );

			DMS_Flash_Messages::add(
				$flash_id,
				__( 'The options were successfully reset', 'dms' ),
				'success'
			);

			if ( $this->is_theme_settings() ) {
				do_action( 'dms_settings_form_reset', $old_values, $new_values );
			} else {
				do_action( 'dms:settings-form:' . $this->get_id() . ':reset', $old_values, $new_values );
			}
		} else { // The "Save" button was pressed
			$new_values = dms_get_options_values_from_input( $this->get_options() );

			$this->set_values( $new_values );

			DMS_Flash_Messages::add(
				$flash_id,
				__( 'The options were successfully saved', 'dms' ),
				'success'
			);

			if ($this->is_theme_settings()) {
				do_action('dms_settings_form_saved', $old_values, $new_values);
			} else {
				do_action('dms:settings-form:'. $this->get_id() .':saved', $old_values, $new_values);
			}
		}

		$data['redirect'] = dms_current_url();

		return $data;
	}

	/**
	 * @internal
	 */
	public function _action_admin_print_footer_scripts() {
		?>
		<script type="text/javascript">
			(function ($) {
				var dmsLoadingId = 'dms-settings-form:<?php echo esc_js($this->get_id()); ?>';

				<?php if (wp_script_is('dms-option-types')): ?>
				// there are options on the page. show loading now and hide it after the options were initialized
				{
					dms.loading.show(dmsLoadingId);

					dmsEvents.one('dms:options:init', function (data) {
						dms.loading.hide(dmsLoadingId);
					});
				}
				<?php endif; ?>

				$(function ($) {
					$(document.body).on({
						'dms:settings-form:before-html-reset': function () {
							dms.loading.show(dmsLoadingId);
						},
						'dms:settings-form:reset': function () {
							dms.loading.hide(dmsLoadingId);
						}
					});
				});
			})(jQuery);
		</script>
		<?php
	}
}
