<?php if (!defined('DM')) die('Forbidden');


function dm_db_option_storage_save($id, array $option, $value, array $params = array()) {
	if (
		!empty($option['dm-storage'])
		&&
		($storage = is_array($option['dm-storage'])
			? $option['dm-storage']
			: array('type' => $option['dm-storage'])
		)
		&&
		!empty($storage['type'])
		&&
		($storage_type = dm_db_option_storage_type($storage['type']))
	) {
		$option['dm-storage'] = $storage;
	} else {
		return $value;
	}

	/** @var dm_Option_Storage_Type $storage_type */

	return $storage_type->save($id, $option, $value, $params);
}

/**
 * @param string $id
 * @param array $option
 * @param mixed $value
 * @param array $params
 *
 * @return mixed
 *
 * @since 2.5.0
 */
function dm_db_option_storage_load($id, array $option, $value, array $params = array()) {
	if (
		!empty($option['dm-storage'])
		&&
		($storage = is_array($option['dm-storage'])
			? $option['dm-storage']
			: array('type' => $option['dm-storage'])
		)
		&&
		!empty($storage['type'])
		&&
		($storage_type = dm_db_option_storage_type($storage['type']))
	) {
		
		if (isset($params['customizer']) && is_customize_preview()) {
			/** @var WP_Customize_Manager $wp_customize */
			global $wp_customize;

			if (
				($setting = $wp_customize->get_setting($setting_id = 'dm_options[' . $id . ']'))
				&&
				!is_null($wp_customize->post_value($setting))
			) {
				
				return $value;
			}
		}

		$option['dm-storage'] = $storage;
	} else {
		return $value;
	}

	/** @var dm_Option_Storage_Type $storage_type */

	return $storage_type->load($id, $option, $value, $params);
}


