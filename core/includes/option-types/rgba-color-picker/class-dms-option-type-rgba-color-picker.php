<?php if ( ! defined( 'DMS' ) ) {
	die( 'Forbidden' );
}

/**
 * RGBA Color Picker
 */
class DMS_Option_Type_Rgba_Color_Picker extends DMS_Option_Type {
	/**
	 * @internal
	 */
	public function _get_backend_width_type() {
		return 'auto';
	}

	/**
	 * @internal
	 * {@inheritdoc}
	 */
	protected function _enqueue_static( $id, $option, $data ) {

		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_style(
			'dms-option-' . $this->get_type(),
			dms_get_framework_directory_uri( '/includes/option-types/' . $this->get_type() . '/static/css/styles.css' ),
			array(),
			dms()->manifest->get_version()
		);

		wp_enqueue_script(
			'dms-option-' . $this->get_type(),
			dms_get_framework_directory_uri( '/includes/option-types/' . $this->get_type() . '/static/js/scripts.js' ),
			array( 'dms-events', 'iris' ),
			dms()->manifest->get_version(),
			true
		);

		wp_localize_script(
			'dms-option-' . $this->get_type(),
			'_dms_option_type_' . str_replace( '-', '_', $this->get_type() ) . '_localized',
			array( 'l10n' => array( 'reset_to_default' => esc_html__( 'Reset', 'dms' ) ) )
		);
	}

	public function get_type() {
		return 'rgba-color-picker';
	}

	/**
	 * @param string $id
	 * @param array $option
	 * @param array $data
	 *
	 * @return string
	 */
	protected function _render( $id, $option, $data ) {
		$option['attr']['value'] = $data['value'];
		$option['attr']['data-default'] = $option['value'];

		$option['attr']['data-palettes'] = ! empty( $option['palettes'] ) && is_array( $option['palettes'] ) ? json_encode( $option['palettes'] ) : '';

		return '<input type="text" ' . dms_attr_to_html( $option['attr'] ) . ' data-alpha="true">';
	}

	/**
	 * @internal
	 */
	protected function _get_value_from_input( $option, $input_value ) {
		if (
			is_null($input_value)
			||
			(
				
				!empty($input_value)
				&&
				!(
					preg_match( '/^#([a-f0-9]{3}){1,2}$/i', $input_value )
					||
					preg_match( '/^rgba\( *([01]?\d\d?|2[0-4]\d|25[0-5]) *\, *([01]?\d\d?|2[0-4]\d|25[0-5]) *\, *([01]?\d\d?|2[0-4]\d|25[0-5]) *\, *(1|0|0?.\d+) *\)$/', $input_value )
				)
			)
		) {
			return (string)$option['value'];
		} else {
			return (string)$input_value;
		}
	}

	/**
	 * @internal
	 */
	protected function _get_defaults() {
		return array(
			'value' => '',
			'palettes'=> true
		);
	}
}