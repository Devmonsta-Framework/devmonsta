<?php if ( ! defined( 'DMS' ) ) {
	die( 'Forbidden' );
}

class DMS_Option_Type_Slider_Short extends DMS_Option_Type_Slider {
	public function get_type() {
		return 'short-slider';
	}

	protected function _render( $id, $option, $data ) {
		$option['attr']['class'] .= ' short-slider dms-option-type-slider';

		return parent::_render( $id, $option, $data );
	}
}