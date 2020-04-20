<?php if (!defined('DM')) die('Forbidden');

class DM_Request
{
	protected static function prepare_key($key)
	{
		return (get_magic_quotes_gpc() && is_string($key) ? addslashes($key) : $key);
	}

	protected static function get_set_key($multikey = null, $set_value = null, &$value)
	{
		$multikey = self::prepare_key($multikey);

		if ($set_value === null) { // get
			return dm_stripslashes_deep_keys($multikey === null ? $value : dm_akg($multikey, $value));
		} else { // set
			dm_aks($multikey, dm_addslashes_deep_keys($set_value), $value);
		}

		return '';
	}

	public static function GET($multikey = null, $default_value = null)
	{
		return dm_stripslashes_deep_keys(
			$multikey === null
				? $_GET
				: dm_akg($multikey, $_GET, $default_value)
		);
	}

	public static function POST($multikey = null, $default_value = null)
	{
		return dm_stripslashes_deep_keys(
			$multikey === null
				? $_POST
				: dm_akg($multikey, $_POST, $default_value)
		);
	}

	public static function COOKIE($multikey = null, $set_value = null, $expire = 0, $path = null)
	{
		if ($set_value !== null) {

			// transforms a string ( key1/key2/key3 => key1][key2][key3] )
			$multikey = str_replace('/', '][', $multikey) . ']';

			// removes the first closed square bracket ( key1][key2][key3] => key1[key2][key3] )
			$multikey = preg_replace('/\]/', '', $multikey, 1);

			return setcookie($multikey, $set_value, $expire, $path);
		} else {
			return self::get_set_key($multikey, $set_value, $_COOKIE);
		}
	}

	public static function REQUEST($multikey = null, $default_value = null)
	{
		return dm_stripslashes_deep_keys(
			$multikey === null
				? $_REQUEST
				: dm_akg($multikey, $_REQUEST, $default_value)
		);
	}
}
