<?php if (!defined('DM')) die('Forbidden');

abstract class DMS_Option_Storage_Type extends DMS_Type {
	/**
	 * Save the value in another place and return a value that will be save in regular place (same as before this feature)
	 *
	 * @param string $id
	 * @param array $option
	 * @param mixed $value Current option (regular) value
	 * @param array $params
	 *
	 * @return mixed
	 *
	 *  - if the save is fail
	 *
	 *    <?php
	 *    return $value;
	 *
	 *  - if the save is success, return a valid default/empty value from that option type,
	 *    so if the separately saved value is lost, will be used empty but valid $value
	 *    and there will be no notices/errors in scripts that are using it and expects a specific structure/format
	 *
	 *    <?php
	 *    return return dms()->backend->option_type($option['type'])->get_value_from_input(array('type' => $option['type']), null);
	 */
	abstract protected function _save($id, array $option, $value, array $params);

	/**
	 * Load the value saved in custom place
	 *
	 * @param string $id
	 * @param array $option
	 * @param mixed $value Current option (regular) value
	 * @param array $params
	 *
	 * @return mixed
	 */
	abstract protected function _load($id, array $option, $value, array $params);

	/**
	 * @param string $id
	 * @param array $option
	 * @param mixed $value
	 * @param array $params
	 *
	 * @return mixed
	 */
	final public function save($id, array $option, $value, array $params = array()) {
		if (
			!empty($option['dms-storage'])
			&&
			($storage = is_array($option['dms-storage'])
				? $option['dms-storage']
				: array('type' => $option['dms-storage'])
			)
			&&
			!empty($storage['type'])
			&&
			$storage['type'] === $this->get_type()
		) {
			$option['dms-storage'] = $storage;
		} else {
			return $value;
		}

		return $this->_save($id, $option, $value, $params);
	}

	/**
	 * @param string $id
	 * @param array $option
	 * @param mixed $value
	 * @param array $params
	 *
	 * @return mixed
	 */
	final public function load($id, array $option, $value, array $params = array()) {
		if (
			!empty($option['dms-storage'])
			&&
			($storage = is_array($option['dms-storage'])
				? $option['dms-storage']
				: array('type' => $option['dms-storage'])
			)
			&&
			!empty($storage['type'])
			&&
			$storage['type'] === $this->get_type()
		) {
			$option['dms-storage'] = $storage;
		} else {
			return $value;
		}

		return $this->_load($id, $option, $value, $params);
	}
}
