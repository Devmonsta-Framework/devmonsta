<?php if (!defined('DMS')) die('Forbidden');

class DMS_Option_Type_Multi_Picker extends DMS_Option_Type
{
	/**
	 * @internal
	 */
	public function _get_backend_width_type()
	{
		return 'full';
	}

	/**
	 * @internal
	 */
	protected function _get_defaults()
	{
		return array(
			'picker' => array(
				'default' => array(
					'type' => 'select',
					'choices' => array()
				)
			),
			'choices' => array(),
			'hide_picker' => false,
			/**
			 * Display separators between options
			 */
			'show_borders' => false,
			'value' => array()
		);
	}

	/**
	 * @internal
	 * {@inheritdoc}
	 */
	protected function _enqueue_static($id, $option, $data)
	{
		static $enqueue = true;
		$uri = dms_get_framework_directory_uri('/includes/option-types/' . $this->get_type());

		if ($enqueue) {
			wp_enqueue_style(
				'dms-option-type-' . $this->get_type(),
				$uri . '/static/css/multi-picker.css',
				array(),
				dms()->manifest->get_version()
			);

			wp_enqueue_script(
				'dms-option-type-' . $this->get_type(),
				$uri . '/static/js/multi-picker.js',
				array('jquery', 'dms-events'),
				dms()->manifest->get_version(),
				true
			);

			$enqueue = false;
		}

		dms()->backend->enqueue_options_static($this->prepare_option($id, $option));

		return true;
	}

	public function _get_data_for_js($id, $option, $data = array()) {
		return false;
	}

	/**
	 * Hide label for each multi-picker by default
	 */
	public function _default_label($id, $option) {
		return false;
	}

	/**
	 * @internal
	 * {@inheritdoc}
	 */
	protected function _render($id, $option, $data)
	{
		$options_array = $this->prepare_option($id, $option);

		unset($option['attr']['name'], $option['attr']['value']);

		if ($option['show_borders']) {
			$option['attr']['class'] .= ' dms-option-type-multi-picker-with-borders';
		} else {
			$option['attr']['class'] .= ' dms-option-type-multi-picker-without-borders';
		}

		$option['attr']['class'] .= is_array(
			$option['picker']
		) ? '' : ' dms-option-type-multi-picker-dynamic';

		// Allow picker to be another option in the same context
		// JS will watch its changes accordingly
		if (is_string($option['picker'])) {
			$option['attr']['data-dms-dynamic-picker-path'] = $option['picker'];
		}

		/**
		 * Leave only select choice options to be rendered in the browser
		 * the rest move to attr[data-options-template] to be rendered on choice change.
		 * This should improve page loading speed.
		 */
		$theme_has_lazy_multi_picker = dms()->theme->get_config(
			'lazy_multi_picker', true
		);

		if (is_array($option['picker']) && $theme_has_lazy_multi_picker) {
			{
				reset($option['picker']);
				$picker_key   = key($option['picker']);
				$picker_type  = $option['picker'][$picker_key]['type'];
				$picker       = $option['picker'][$picker_key];

				if ( ! is_string($picker_value = dms()->backend->option_type($picker_type)->get_value_from_input(
					$picker, isset($data['value'][$picker_key]) ? $data['value'][$picker_key] : null
				))) {
					/**
					 * Extract the string value that is used as array key
					 */
					switch ($picker_type) {
						case 'icon-v2':
							$picker_value = dms_akg('type', $picker_value, 'icon-font');
							break;
						default:
							if ( ! is_string($picker_value = apply_filters(
								'dms:option-type:multi-picker:string-value:'. $picker_type, // @since 2.5.8
								$picker_value
							))) {
								trigger_error(
									'[multi-picker] Cannot detect string value for picker type '. $picker_type,
									E_USER_WARNING
								);
								$picker_value = '?';
							}
					}
				}
			}

			$skip_first = true;
			foreach ($options_array as $group_id => &$group) {
				if ($skip_first) {
					$skip_first = false;
					continue; // first is picker
				}

				if ($group_id === $id .'-'. $picker_value) {
					continue; // skip selected choice options
				}

				$options_array[$group_id]['attr']['data-options-template'] = dms()->backend->render_options(
					$options_array[$group_id]['options'], $data['value'], array(
					'id_prefix' => $data['id_prefix'] . $id . '-',
					'name_prefix' => $data['name_prefix'] . '[' . $id . ']',
				));

				$options_array[$group_id]['options'] = array();
			}
		}

		return '<div ' . dms_attr_to_html($option['attr']) . '>' .
			dms()->backend->render_options($options_array, $data['value'], array(
				'id_prefix' => $data['id_prefix'] . $id . '-',
				'name_prefix' => $data['name_prefix'] . '[' . $id . ']',
			)) .
		'</div>';
	}

	public function get_type()
	{
		return 'multi-picker';
	}

	/**
	 * @param array $option
	 * @param array $picker
	 * @param string $picker_type
	 * @return array( 'choice_id' => array( Choice Options ) )
	 */
	private function get_picker_choices($option) {
		return $option['choices'];

		switch($picker_type) {
			case 'switch':
				$picker_choices = array_intersect_key($option['choices'], array(
					$picker['left-choice']['value']  => array(),
					$picker['right-choice']['value'] => array()
				));
				break;
			case 'select':
			case 'short-select':
				// we need to treat the case with optgroups
				$collected_choices = array();
				foreach ($picker['choices'] as $key => $value) {
					if (is_array($value) && isset($value['choices'])) {
						// we have an optgroup
						$collected_choices = array_merge($collected_choices, $value['choices']);
					} else {
						$collected_choices[$key] = $value;
					}
				}
				$picker_choices = array_intersect_key($option['choices'], $collected_choices);
				break;
			case 'radio':
			case 'image-picker':
				$picker_choices = array_intersect_key($option['choices'], $picker['choices']);
				break;
			case 'icon-v2':
				$picker_choices = array_intersect_key(
					$option['choices'],
					array(
						'icon-font' => array(),
						'custom-upload' => array()
					)
				);
				break;
			default:
				$picker_choices = apply_filters(
					'dms_option_type_multi_picker_choices:'. $picker_type,
					$option['choices'],
					array(
						'picker' => $picker,
						'option' => $option,
					)
				);
		}

		return $picker_choices;
	}

	private function prepare_option($id, $option)
	{
		if (empty($option['picker'])) {
			trigger_error(
				sprintf(__('No \'picker\' key set for multi-picker option: %s', 'dms'), $id),
				E_USER_ERROR
			);
		}
		
		/**
		 * @since 2.6.11
		 */
		$option = $this->prepare_choices($option);


		$picker_choices = $this->get_picker_choices(
			$option
		);

		$hide_picker = '';
		$show_borders = '';

		if (
			1 === count($picker_choices)
			&&
			isset($option['hide_picker'])
			&&
			true === $option['hide_picker']
		) {
			$hide_picker = 'dms-hidden';
		}

		if (
			isset($option['show_borders'])
			&&
			true === $option['show_borders']
		) {
			$show_borders = 'dms-show-borders dms-option-type-multi-show-borders';
		}

		$choices_groups = array();

		foreach ($picker_choices as $key => $set) {
			if (!empty($set)) {
				$choices_groups[$id . '-' . $key] = array(
					'type'    => 'group',
					'attr'    => array(
						'class' => 'choice-group',
						'data-choice-key' => $key,
					),
					'options' => array(
						$key => array(
							'type'          => 'multi',
							'attr'          => array('class' => $show_borders),
							'label'         => false,
							'desc'          => false,
							'inner-options' => $set
						)
					)
				);
			}
		}

		$picker_group = null;

		if (is_array($option['picker'])) {
			{
				reset($option['picker']);
				$picker_key  = key($option['picker']);
				$picker      = $option['picker'][$picker_key];
				$picker_type = $picker['type'];
			}

			$picker_group = array(
				$id . '-picker' => array(
					'type'    => 'group',
					'desc'    => false,
					'label'   => false,
					'attr'    => array('class' => $show_borders .' '. $hide_picker .' picker-group picker-type-'. $picker_type),
					'options' => array($picker_key => $picker)
				)
			);

		}

		return $picker_group ? array_merge($picker_group, $choices_groups) : $choices_groups;
	}
	
	/**
	 * Prepare `choices` array.
	 *
	 * @since 2.6.11
	 * @param array $option Options.
	 * @return array
	 */
	protected function prepare_choices($option) {
		$result = array();
		$choices = dms_akg('choices', $option);

		if (is_array($choices)) {
			foreach ($choices as $key => $settings) {
				if (isset($settings['for']) && isset($settings['options'])) {
					if (is_array($settings['for'])) {
						// Insert location: after/before.
						$location = dms_akg('location', $settings, 'before');
						
						foreach ($settings['for'] as $name) {
							if (isset($choices[$name])) {
								if ('before' === $location) {
									$result[$name] = array_merge(
										$settings['options'], $choices[$name]
									);
								} else {
									$result[$name] = array_merge(
										$choices[$name], $settings['options']
									);
								}
							} else {
								if (isset($result[$name])) {
									if ('before' === $location) {
										$result[$name] = array_merge(
											$settings['options'], $result[$name]
										);
									} else {
										$result[$name] = array_merge(
											$result[$name], $settings['options']
										);
									}
								} else {
									$result[$name] = $settings['options'];
								}
							}
						}
					}
				} else {
					if ( ! isset($result[$key]) ) {
						$result[$key] = $settings;
					}
				}
			}
		}

		// Replace old `choices` with new structure.
		dms_aks('choices', $result, $option);
		
		return $option;
	}

	/**
	 * @internal
	 */
	protected function _get_value_from_input($option, $input_value)
	{
		$value = array();
		
		/**
		 * @since 2.6.11
		 */
		$option = $this->prepare_choices($option);

		if (is_array($option['picker'])) {
			reset($option['picker']);
			$picker_key  = key($option['picker']);
			$picker_type = $option['picker'][$picker_key]['type'];
			$picker      = $option['picker'][$picker_key];

			if (is_null($input_value) && isset($option['value'][$picker_key])) {
				$value[$picker_key] = $option['value'][$picker_key];
			} else {
				$value[$picker_key] = dms()->backend->option_type($picker_type)->get_value_from_input(
					$picker,
					isset($input_value[$picker_key]) ? $input_value[$picker_key] : null
				);
			}
		}

		foreach (
			$this->get_picker_choices(
				$option
			)
			as $choice_id => $choice_options
		) {
			if (is_null($input_value) && isset($option['value'][$choice_id])) {
				$value[$choice_id] = $option['value'][$choice_id];
			} else {
				foreach (dms_extract_only_options($choice_options) as $choice_option_id => $choice_option) {
					$value[$choice_id][$choice_option_id] = dms()->backend->option_type($choice_option['type'])->get_value_from_input(
						$choice_option,
						isset($input_value[$choice_id][$choice_option_id]) ? $input_value[$choice_id][$choice_option_id] : null
					);
				}
			}
		}

		return $value;
	}


	protected function _storage_load($id, array $option, $value, array $params) {
		if (apply_filters('dms:option-type:multi-picker:dms-storage:process-inner-options', false)) {
			foreach ($option['choices'] as $choice_id => $choice) {
				foreach (dms_extract_only_options($choice) as $opt_id => $opt) {
					$value[$choice_id][$opt_id] = dms()->backend->option_type($opt['type'])->storage_load(
						$opt_id, $opt, $value[$choice_id][$opt_id], $params
					);
				}
			}
		}

		return dms_db_option_storage_load($id, $option, $value, $params);
	}

	
	protected function _storage_save($id, array $option, $value, array $params) {
		if (apply_filters('dms:option-type:multi-picker:dms-storage:process-inner-options', false)) {
			foreach ($option['choices'] as $choice_id => $choice) {
				foreach (dms_extract_only_options($choice) as $opt_id => $opt) {
					$value[$choice_id][$opt_id] = dms()->backend->option_type($opt['type'])->storage_save(
						$opt_id, $opt, $value[$choice_id][$opt_id], $params
					);
				}
			}
		}

		return dms_db_option_storage_save($id, $option, $value, $params);
	}
}
