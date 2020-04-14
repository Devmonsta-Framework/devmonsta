<?php if ( ! defined( 'DM' ) ) {
	die( 'Forbidden' );
}

/**
 * Features:
 * - Works with "multi keys"
 */
class DM_WP_Meta {
	/**
	 * @param string $meta_type
	 * @param int $object_id
	 * @param string $multi_key 'abc' or 'ab/c/def'
	 * @param array|string|int|bool $set_value
	 */
	public static function set( $meta_type, $object_id, $multi_key, $set_value ) {
		if ( empty( $multi_key ) ) {
			trigger_error( 'Key not specified', E_USER_WARNING );
			return;
		}

		$multi_key = explode( '/', $multi_key );
		$key       = array_shift( $multi_key );
		$multi_key = implode( '/', $multi_key );

		if ( empty( $multi_key ) && $multi_key !== '0' ) { // Replace entire meta
			dm_update_metadata( $meta_type, $object_id, $key, $set_value );
		} else { // Change only specified key
			$value = self::get( $meta_type, $object_id, $key, true );
			dm_aks( $multi_key, $set_value, $value );
			dm_update_metadata( $meta_type, $object_id, $key, $value );
		}
	}
	
	public static function get( $meta_type, $object_id, $multi_key, $default_value = null, $get_original_value = null ) {
		if ( ! is_null($get_original_value) ) {
			_doing_it_wrong(__FUNCTION__, '$get_original_value parameter was removed', 'DM 2.5.8');
		}

		if ( empty( $multi_key ) ) {
			trigger_error( 'Key not specified', E_USER_WARNING );
			return null;
		}

		$multi_key = explode( '/', $multi_key );
		$key       = array_shift( $multi_key );
		$multi_key = implode( '/', $multi_key );

		$value = get_metadata( $meta_type, $object_id, $key, true );

		if ( empty( $multi_key ) && $multi_key !== '0' ) {
			return $value;
		} else {
			return dm_akg($multi_key, $value, $default_value);
		}
	}
}
