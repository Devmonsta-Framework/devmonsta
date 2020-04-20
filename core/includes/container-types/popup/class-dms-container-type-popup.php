<?php if (!defined('DM')) die('Forbidden');

class DM_Container_Type_Popup extends DM_Container_Type {
	public function get_type() {
		return 'popup';
	}

	protected function _get_defaults() {
		return array(
			'modal-size' => 'small', // small, medium, large
			'desc' => '',
		);
	}

	protected function _enqueue_static($id, $option, $values, $data) {
		$uri = dm_get_framework_directory_uri('/includes/container-types/popup');

		wp_enqueue_script(
			'dm-container-type-'. $this->get_type(),
			$uri .'/scripts.js',
			array('jquery', 'dm-events', 'dm'),
			dm()->manifest->get_version()
		);

		wp_enqueue_style('dm');

		wp_enqueue_style(
			'dm-container-type-'. $this->get_type(),
			$uri .'/styles.css',
			array(),
			dm()->manifest->get_version()
		);
	}

	protected function _render($containers, $values, $data) {
		$html = '';

		$defaults = $this->get_defaults();

		foreach ($containers as $id => &$option) {
			{
				$attr = $option['attr'];

				$attr['data-modal-title'] = $option['title'];

				if (in_array($option['modal-size'], array('small', 'medium', 'large'))) {
					$attr['data-modal-size'] = $option['modal-size'];
				} else {
					$attr['data-modal-size'] = $defaults['modal-size'];
				}

				$attr['id'] = $data['id_prefix'] . $id;
			}

			$html .=
				'<div '. dm_attr_to_html($attr) .'>'
				. '<p class="popup-button-wrapper">'
				. dm_html_tag(
					'button',
					array(
						'type' => 'button',
						'class' => 'button button-secondary popup-button',
					),
					$option['title']
				)
				. '</p>'
				. (empty($option['desc']) ? '' : ('<div class="popup-desc">'. $option['desc'] .'</div>'))
				. '<div class="popup-options dm-hidden">'
				. dm()->backend->render_options($option['options'], $values, $data)
				. '</div>'
				. '</div>';
		}

		return $html;
	}
}
