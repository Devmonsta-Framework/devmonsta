<?php if (!defined('DMS')) die('Forbidden');

class DMS_Option_Type_Addable_Option extends DMS_Option_Type
{
	public function get_type()
	{
		return 'addable-option';
	}

	/**
	 * @internal
	 */
	protected function _get_defaults()
	{
		return array(
			'value'  => array(),
			'option' => array(
				'type' => 'text',
			),
			'add-button-text' => __('Add', 'dms'),
			/**
			 * Makes the options sortable
			 *
			 * You can disable this in case the options order doesn't matter,
			 * to not confuse the user that if changing the order will affect something.
			 */
			'sortable' => true,
		);
	}

	protected function _get_data_for_js($id, $option, $data = array()) {
		return false;
	}

	/**
	 * @internal
	 * {@inheritdoc}
	 */
	protected function _enqueue_static($id, $option, $data)
	{
		static $enqueue = true;

		if ($enqueue) {
			wp_enqueue_style(
				'dms-option-'. $this->get_type(),
				dms_get_framework_directory_uri('/includes/option-types/'. $this->get_type() .'/static/css/styles.css'),
				array(),
				dms()->manifest->get_version()
			);

			wp_enqueue_script(
				'dms-option-'. $this->get_type(),
				dms_get_framework_directory_uri('/includes/option-types/'. $this->get_type() .'/static/js/scripts.js'),
				array('dms-events', 'jquery-ui-sortable'),
				dms()->manifest->get_version(),
				true
			);

			$enqueue = false;
		}

		dms()->backend->option_type($option['option']['type'])->enqueue_static();

		return true;
	}

	/**
	 * @internal
	 */
	protected function _render($id, $option, $data)
	{
		return dms_render_view(dms_get_framework_directory('/includes/option-types/'. $this->get_type() .'/view.php'), array(
			'id'     => $id,
			'option' => $option,
			'data'   => $data,
			'move_img_src' => dms_get_framework_directory_uri('/static/img/sort-vertically.png'),
		));
	}

	/**
	 * @internal
	 */
	protected function _get_value_from_input($option, $input_value)
	{
		if (!is_array($input_value)) {
			return $option['value'];
		}

		$option_type = dms()->backend->option_type($option['option']['type']);

		$value = array();

		foreach ($input_value as $option_input_value) {
			$value[] = $option_type->get_value_from_input(
				$option['option'],
				$option_input_value
			);
		}

		return $value;
	}
}
