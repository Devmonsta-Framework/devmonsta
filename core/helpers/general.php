<?php if (!defined('DM')) {
	die('Forbidden');
}
// Useful functions



/**
 * print_r() alternative
 *
 * @param mixed $value Value to debug
 */
function dm_print($value)
{
	static $first_time = true;

	if ($first_time) {
		ob_start();
		echo '<style type="text/css">
		div.dm_print_r {
			max-height: 500px;
			overflow-y: scroll;
			background: #23282d;
			margin: 10px 30px;
			padding: 0;
			border: 1px solid #F5F5F5;
			border-radius: 3px;
			position: relative;
			z-index: 11111;
		}

		div.dm_print_r pre {
			color: #78FF5B;
			background: #23282d;
			text-shadow: 1px 1px 0 #000;
			font-family: Consolas, monospace;
			font-size: 12px;
			margin: 0;
			padding: 5px;
			display: block;
			line-height: 16px;
			text-align: left;
		}

		div.dm_print_r_group {
			background: #f1f1f1;
			margin: 10px 30px;
			padding: 1px;
			border-radius: 5px;
			position: relative;
			z-index: 11110;
		}

		div.dm_print_r_group div.dm_print_r {
			margin: 9px;
			border-width: 0;
		}
		</style>';
		echo str_replace(array('  ', "\n"), '', ob_get_clean());

		$first_time = false;
	}

	if (func_num_args() == 1) {
		echo '<div class="dm_print_r"><pre>';
		echo dm_htmlspecialchars(Dm_Dumper::dump($value));
		echo '</pre></div>';
	} else {
		echo '<div class="dm_print_r_group">';
		foreach (func_get_args() as $param) {
			dm_print($param);
		}
		echo '</div>';
	}
}

/**
 * Alias for dm_print
 *
 * @see dm_print()
 */
if (!function_exists('debug')) {
	function debug()
	{
		call_user_func_array('dm_print', func_get_args());
	}
}


/**
 * Use this id do not need to enter every time same last two parameters
 * Info: Cannot use default parameters because in php 5.2 encoding is not UTF-8 by default
 *
 * @param string $string
 *
 * @return string
 */
function dm_htmlspecialchars($string)
{
	return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}


/**
 * Recursively find a key's value in array
 *
 * @param string $keys 'a/b/c'
 * @param array|object $array_or_object
 * @param null|mixed $default_value
 * @param string $keys_delimiter
 *
 * @return null|mixed
 */
function dm_array_key_get($keys, $array_or_object, $default_value = null, $keys_delimiter = '/')
{
	if (!is_array($keys)) {
		$keys = explode($keys_delimiter, (string) $keys);
	}

	$array_or_object = dm_call($array_or_object);

	$key_or_property = array_shift($keys);
	if ($key_or_property === null) {
		return dm_call($default_value);
	}

	$is_object = is_object($array_or_object);

	if ($is_object) {
		if (!property_exists($array_or_object, $key_or_property)) {
			return dm_call($default_value);
		}
	} else {
		if (!is_array($array_or_object) || !array_key_exists($key_or_property, $array_or_object)) {
			return dm_call($default_value);
		}
	}

	if (isset($keys[0])) { // not used count() for performance reasons
		if ($is_object) {
			return dm_array_key_get($keys, $array_or_object->{$key_or_property}, $default_value);
		} else {
			return dm_array_key_get($keys, $array_or_object[$key_or_property], $default_value);
		}
	} else {
		if ($is_object) {
			return $array_or_object->{$key_or_property};
		} else {
			return $array_or_object[$key_or_property];
		}
	}
}

/**
 * Set (or create if not exists) value for specified key in some array level
 *
 * @param string $keys 'a/b/c', or 'a/b/c/' equivalent to: $arr['a']['b']['c'][] = $val;
 * @param mixed $value
 * @param array|object $array_or_object
 * @param string $keys_delimiter
 *
 * @return array|object
 */
function dm_array_key_set($keys, $value, &$array_or_object, $keys_delimiter = '/')
{
	if (!is_array($keys)) {
		$keys = explode($keys_delimiter, (string) $keys);
	}

	$key_or_property = array_shift($keys);
	if ($key_or_property === null) {
		return $array_or_object;
	}

	$is_object = is_object($array_or_object);

	if ($is_object) {
		if (
			!property_exists($array_or_object, $key_or_property)
			|| !(is_array($array_or_object->{$key_or_property}) || is_object($array_or_object->{$key_or_property}))
		) {
			if ($key_or_property === '') {
				// this happens when use 'empty keys' like: abc/d/e////i/j//foo/
				trigger_error('Cannot push value to object like in array ($arr[] = $val)', E_USER_WARNING);
			} else {
				$array_or_object->{$key_or_property} = array();
			}
		}
	} else {
		if (!is_array($array_or_object)) {
			$array_or_object = array();
		}

		if (
			!array_key_exists(
				$key_or_property,
				$array_or_object
			) || !is_array($array_or_object[$key_or_property])
		) {
			if ($key_or_property === '') {
				// this happens when use 'empty keys' like: abc.d.e....i.j..foo.
				$array_or_object[] = array();

				// get auto created key (last)
				end($array_or_object);
				$key_or_property = key($array_or_object);
			} else {
				$array_or_object[$key_or_property] = array();
			}
		}
	}

	if (isset($keys[0])) { // not used count() for performance reasons
		if ($is_object) {
			dm_array_key_set($keys, $value, $array_or_object->{$key_or_property});
		} else {
			dm_array_key_set($keys, $value, $array_or_object[$key_or_property]);
		}
	} else {
		if ($is_object) {
			$array_or_object->{$key_or_property} = $value;
		} else {
			$array_or_object[$key_or_property] = $value;
		}
	}

	return $array_or_object;
}


/**
 * Unset specified key in some array level
 * @param string $keys 'a/b/c' -> unset($arr['a']['b']['c']);
 * @param array|object $array_or_object
 * @param string $keys_delimiter
 * @return array|object
 */
function dm_array_key_unset($keys, &$array_or_object, $keys_delimiter = '/')
{
	if (!is_array($keys)) {
		$keys = explode($keys_delimiter, (string) $keys);
	}

	$key_or_property = array_shift($keys);
	if ($key_or_property === null || $key_or_property === '') {
		return $array_or_object;
	}

	$is_object = is_object($array_or_object);

	if ($is_object) {
		if (!property_exists($array_or_object, $key_or_property)) {
			return $array_or_object;
		}
	} else {
		if (!is_array($array_or_object) || !array_key_exists($key_or_property, $array_or_object)) {
			return $array_or_object;
		}
	}

	if (isset($keys[0])) { // not used count() for performance reasons
		if ($is_object) {
			dm_array_key_unset($keys, $array_or_object->{$key_or_property});
		} else {
			dm_array_key_unset($keys, $array_or_object[$key_or_property]);
		}
	} else {
		if ($is_object) {
			unset($array_or_object->{$key_or_property});
		} else {
			unset($array_or_object[$key_or_property]);
		}
	}

	return $array_or_object;
}



/**
 * If the value is instance of Dm_Callback class then it is executed and returns the callback value
 * In other case function returns the provided value
 *
 * @param mixed|Dm_Callback $value
 *
 * @return mixed
 *
 * @since 2.6.14
 */
function dm_call($value)
{
	if (!dm_is_callback($value)) {
		return $value;
	}

	return (is_object($value) && get_class($value) == 'Closure')
		? $value()
		: $value->execute();
}

/**
 * Check if the current value is instance of Dm_Callback class
 *
 * @param mixed $value
 *
 * @return bool
 */
function dm_is_callback($value)
{
	return $value instanceof Dm_Callback || (is_object($value) && get_class($value) == 'Closure');
}

/**
 * Convert bytes to human readable format
 * @param integer $bytes Size in bytes to convert
 * @param integer $precision
 * @return string
 * @since 1.0.0
 */
function dm_human_bytes($bytes, $precision = 2)
{
	$kilobyte = 1024;
	$megabyte = $kilobyte * 1024;
	$gigabyte = $megabyte * 1024;
	$terabyte = $gigabyte * 1024;

	if (($bytes >= 0) && ($bytes < $kilobyte)) {
		return $bytes . ' B';
	} elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
		return round($bytes / $kilobyte, $precision) . ' KB';
	} elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
		return round($bytes / $megabyte, $precision) . ' MB';
	} elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
		return round($bytes / $gigabyte, $precision) . ' GB';
	} elseif ($bytes >= $terabyte) {
		return round($bytes / $terabyte, $precision) . ' TB';
	} else {
		return $bytes . ' B';
	}
}


/**
 * Generate random unique md5
 */
function dm_rand()
{
	return md5(time() . '-' . uniqid(rand(), true) . '-' . mt_rand(1, 1000));
}


/**
 * Search relative path in child then in parent theme directory and return URI
 * @param  string $rel_path '/some/path_to_dir' or '/some/path_to_file.php'
 * @return string URI
 */
function dm_theme_path_uri($rel_path)
{
	if (is_child_theme() && file_exists(get_stylesheet_directory() . $rel_path)) {
		return get_stylesheet_directory_uri() . $rel_path;
	} elseif (file_exists(get_template_directory() . $rel_path)) {
		return get_template_directory_uri() . $rel_path;
	} else {
		return 'about:blank#theme-file-not-found:' . $rel_path;
	}
}

/**
 * Search relative path in child then in parent theme directory and return full path
 * @param  string $rel_path '/some/path_to_dir' or '/some/path_to_file.php'
 * @return string URI
 */
function dm_theme_path($rel_path)
{
	if (is_child_theme() && file_exists(get_stylesheet_directory() . $rel_path)) {
		return get_stylesheet_directory() . $rel_path;
	} elseif (file_exists(get_template_directory() . $rel_path)) {
		return get_template_directory() . $rel_path;
	} else {
		return false;
	}
}

/**
 * Convert to Unix style directory separators
 */
function dm_fix_path($path)
{
	$windows_network_path = isset($_SERVER['windir']) && in_array(
		substr($path, 0, 2),
		array('//', '\\\\'),
		true
	);
	$fixed_path           = untrailingslashit(str_replace(array('//', '\\'), array('/', '/'), $path));

	if (empty($fixed_path) && !empty($path)) {
		$fixed_path = '/';
	}

	if ($windows_network_path) {
		$fixed_path = '//' . ltrim($fixed_path, '/');
	}

	return $fixed_path;
}

	/**
	 * Full path to the parent-theme directory
	 * @param string $rel_path
	 * @return string
	 */
	function dm_get_framework_directory($rel_path = '')
	{
		try {
			$dir = Dm_Cache::get($cache_key = 'dm_framework_dir');
		} catch (Dm_Cache_Not_Found_Exception $e) {
			Dm_Cache::set(
				$cache_key,
				$dir = apply_filters(
					'dm_framework_directory',
					dm_fix_path(dirname(dirname(__FILE__))) // double dirname() to remove '/helpers', use parent dir
				)
			);
		}

		return $dir . $rel_path;
	}

	/**
	 * URI to the parent-theme directory
	 *
	 * @param string $rel_path
	 *
	 * @return string
	 */
	function dm_get_framework_directory_uri($rel_path = '')
	{
		try {
			$uri = Dm_Cache::get($cache_key = 'dm_framework_dir_uri');
		} catch (Dm_Cache_Not_Found_Exception $e) {
			Dm_Cache::set(
				$cache_key,
				$uri = apply_filters(
					'dm_framework_directory_uri',
					($uri = dm_get_path_url(dm_get_framework_directory())) ? $uri: get_template_directory_uri() 
				)
			);
		}

		return $uri . $rel_path;
	}