<?php if ( ! defined( 'DMS' ) ) {
	die( 'Forbidden' );
}

/**
 * Slider
 * -----*--
 */
class DMS_Option_Type_Slider extends DMS_Option_Type {

	/**
	 * This class is extended by 'short-slider' option type
	 * but the type here should be this
	 * @return string
	 */
	private function _get_type() {
		return 'slider';
	}

	protected function _get_data_for_js($id, $option, $data = array()) {
		return false;
	}

	/**
	 * @internal
	 * {@inheritdoc}
	 */
	protected function _enqueue_static( $id, $option, $data ) {
		{
			wp_enqueue_style(
				'dms-option-' . $this->_get_type() . 'ion-range-slider',
				dms_get_framework_directory_uri( '/includes/option-types/' . $this->_get_type() . '/static/libs/ion-range-slider/ion.rangeSlider.css' ),
				dms()->manifest->get_version()
			);

			wp_enqueue_script(
				'dms-option-' . $this->_get_type() . 'ion-range-slider',
				dms_get_framework_directory_uri( '/includes/option-types/' . $this->_get_type() . '/static/libs/ion-range-slider/ion.rangeSlider.min.js' ),
				array( 'jquery', 'dms-moment' ),
				dms()->manifest->get_version()
			);
		}

		wp_enqueue_style(
			'dms-option-' . $this->_get_type(),
			dms_get_framework_directory_uri( '/includes/option-types/' . $this->_get_type() . '/static/css/styles.css' ),
			dms()->manifest->get_version()
		);

		wp_enqueue_script(
			'dms-option-' . $this->_get_type(),
			dms_get_framework_directory_uri( '/includes/option-types/' . $this->_get_type() . '/static/js/scripts.js' ),
			array( 'jquery',  'dms-events', 'underscore', 'dms-option-' . $this->_get_type() . 'ion-range-slider' ),
			dms()->manifest->get_version()
		);
	}

	public function get_type() {
		return $this->_get_type();
	}

	/**
	 * @internal
	 */
	protected function _render( $id, $option, $data ) {
		$option['properties']['type'] = 'single';
		$option['properties']['from'] = isset( $data['value'] ) ? $data['value'] : $option['value'];

		if(isset($option['properties']['values']) && is_array($option['properties']['values'])){
			$option['properties']['from'] = array_search($option['properties']['from'], $option['properties']['values']);
		}

		$option['attr']['data-dms-irs-options'] = json_encode(
			$this->default_properties($option['properties'])
		);

		return dms_render_view( dms_get_framework_directory( '/includes/option-types/' . $this->_get_type() . '/view.php' ), array(
			'id'     => $id,
			'option' => $option,
			'data'   => $data,
			'value'  => $data['value']
		) );
	}

	private function default_properties($properties = array()) {
		return array_merge(array(
			'min' => 0,
			'max' => 100,
			'step' => 1,
			/**
			 * For large ranges, this will create https://static.md/6340ebf52a36255649f10b3d0dff3b1c.png
			 */
			'grid_snap' => false,
		), $properties);
	}

	/**
	 * @internal
	 */
	protected function _get_defaults() {
		return array(
			'value'      => 0,
			'properties' => $this->default_properties(), // https://github.com/IonDen/ion.rangeSlider#settings
		);
	}

	/**
	 * @internal
	 */
	protected function _get_value_from_input( $option, $input_value ) {
		if (is_null($input_value)) {
			return $option['value'];
		} else {
			return floatval($input_value);
		}
	}

}
