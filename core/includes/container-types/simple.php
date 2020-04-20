<?php if (!defined('DMS')) die('Forbidden');

class DMS_Container_Type_Group extends DMS_Container_Type {
	public function get_type() {
		return 'group';
	}

	protected function _get_defaults() {
		return array();
	}

	protected function _enqueue_static($id, $option, $values, $data) {
		//
	}

	protected function _render($containers, $values, $data) {
		$html = '';

		foreach ( $containers as $id => &$group ) {
			// prepare attributes
			{
				$attr = isset( $group['attr'] ) ? $group['attr'] : array();

				$attr['id'] = 'dms-backend-options-group-' . $id;

				if ( ! isset( $attr['class'] ) ) {
					$attr['class'] = 'dms-backend-options-group';
				} else {
					$attr['class'] = 'dms-backend-options-group ' . $attr['class'];
				}
			}

			$html .= '<div ' . dms_attr_to_html( $attr ) . '>';
			$html .= dms()->backend->render_options( $group['options'], $values, $data );
			$html .= '</div>';
		}

		return $html;
	}
}
