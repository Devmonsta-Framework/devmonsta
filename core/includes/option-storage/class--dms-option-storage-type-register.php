<?php if (!defined('DMS')) die('Forbidden');

/**
 * @internal
 */
class _DMS_Option_Storage_Type_Register extends DMS_Type_Register {
	protected function validate_type(DMS_Type $type) {
		return $type instanceof DMS_Option_Storage_Type;
	}
}
