<?php if (!defined('DMS')) {
	die('Forbidden');
}
// Useful functions


/**
 * Convert to Unix style directory separators
 */

function dms_fix_path($path)
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

function dms_backups_destination_directory()
{
	$uploads = wp_upload_dir();
	return dms_fix_path($uploads['basedir'] .   "/elementor/css/");
}


/** Framework related functions */ {
	/**
	 * Full path to the parent-theme directory
	 *
	 * @param string $rel_path
	 *
	 * @return string
	 */
	function dms_get_framework_directory($rel_path = '')
	{
		try {
			$dir = DMS_Cache::get($cache_key = 'dms_framework_dir');
		} catch (DMS_Cache_Not_Found_Exception $e) {
			DMS_Cache::set(
				$cache_key,
				$dir = apply_filters(
					'dms_framework_directory',
					dms_fix_path(dirname(dirname(__FILE__))) // double dirname() to remove '/helpers', use parent dir
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
	function dms_get_framework_directory_uri($rel_path = '')
	{
		try {
			$uri = DMS_Cache::get($cache_key = 'dms_framework_dir_uri');
		} catch (DMS_Cache_Not_Found_Exception $e) {
			DMS_Cache::set(
				$cache_key,
				$uri = apply_filters(
					'dms_framework_directory_uri',
					($uri = dms_get_path_url(dms_get_framework_directory())) ? $uri: get_template_directory_uri() 
				)
			);
		}

		return $uri . $rel_path;
	}
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
function dms_akg($keys, $array_or_object, $default_value = null, $keys_delimiter = '/')
{
	if (!is_array($keys)) {
		$keys = explode($keys_delimiter, (string) $keys);
	}

	$array_or_object = dms_call($array_or_object);

	$key_or_property = array_shift($keys);
	if ($key_or_property === null) {
		return dms_call($default_value);
	}

	$is_object = is_object($array_or_object);

	if ($is_object) {
		if (!property_exists($array_or_object, $key_or_property)) {
			return dms_call($default_value);
		}
	} else {
		if (!is_array($array_or_object) || !array_key_exists($key_or_property, $array_or_object)) {
			return dms_call($default_value);
		}
	}

	if (isset($keys[0])) { // not used count() for performance reasons
		if ($is_object) {
			return dms_akg($keys, $array_or_object->{$key_or_property}, $default_value);
		} else {
			return dms_akg($keys, $array_or_object[$key_or_property], $default_value);
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
function dms_aks($keys, $value, &$array_or_object, $keys_delimiter = '/')
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
			dms_aks($keys, $value, $array_or_object->{$key_or_property});
		} else {
			dms_aks($keys, $value, $array_or_object[$key_or_property]);
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
 *
 * @param string $keys 'a/b/c' -> unset($arr['a']['b']['c']);
 * @param array|object $array_or_object
 * @param string $keys_delimiter
 *
 * @return array|object
 */
function dms_aku($keys, &$array_or_object, $keys_delimiter = '/')
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
			dms_aku($keys, $array_or_object->{$key_or_property});
		} else {
			dms_aku($keys, $array_or_object[$key_or_property]);
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
 * Generate random unique md5
 */
function dms_rand_md5()
{
	return md5(time() . '-' . uniqid(rand(), true) . '-' . mt_rand(1, 1000));
}



function dms_unique_increment()
{
	static $i = 0;

	return ++$i;
}


/**
 * print_r() alternative
 *
 * @param mixed $value Value to debug
 */
function dms_print($value)
{
	static $first_time = true;

	if ($first_time) {
		ob_start();
		echo '<style type="text/css">
		div.dms_print_r {
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

		div.dms_print_r pre {
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

		div.dms_print_r_group {
			background: #f1f1f1;
			margin: 10px 30px;
			padding: 1px;
			border-radius: 5px;
			position: relative;
			z-index: 11110;
		}
		div.dms_print_r_group div.dms_print_r {
			margin: 9px;
			border-width: 0;
		}
		</style>';
		echo str_replace(array('  ', "\n"), '', ob_get_clean());

		$first_time = false;
	}

	if (func_num_args() == 1) {
		echo '<div class="dms_print_r"><pre>';
		echo dms_htmlspecialchars(DMS_Dumper::dump($value));
		echo '</pre></div>';
	} else {
		echo '<div class="dms_print_r_group">';
		foreach (func_get_args() as $param) {
			dms_print($param);
		}
		echo '</div>';
	}
}

/**
 * Alias for dms_print
 *
 * @see dms_print()
 */
if (!function_exists('debug')) {
	function debug()
	{
		call_user_func_array('dms_print', func_get_args());
	}
}

/**
 * Generate html tag
 *
 * @param string $tag Tag name
 * @param array $attr Tag attributes
 * @param bool|string $end Append closing tag. Also accepts body content
 *
 * @return string The tag's html
 */
function dms_html_tag($tag, $attr = array(), $end = false)
{
	$html = '<' . $tag . ' ' . dms_attr_to_html($attr);

	if ($end === true) {
		# <script></script>
		$html .= '></' . $tag . '>';
	} else if ($end === false) {
		# <br/>
		$html .= '/>';
	} else {
		# <div>content</div>
		$html .= '>' . $end . '</' . $tag . '>';
	}

	return $html;
}

/**
 * Generate attributes string for html tag
 *
 * @param array $attr_array array('href' => '/', 'title' => 'Test')
 *
 * @return string 'href="/" title="Test"'
 */
function dms_attr_to_html(array $attr_array)
{
	$html_attr = '';

	foreach ($attr_array as $attr_name => $attr_val) {
		if ($attr_val === false) {
			continue;
		}

		$html_attr .= $attr_name . '="' . dms_htmlspecialchars($attr_val) . '" ';
	}

	return $html_attr;
}

/**
 * Strip slashes from values, and from keys if magic_quotes_gpc = On
 */
function dms_stripslashes_deep_keys($value)
{
	static $magic_quotes = null;
	if ($magic_quotes === null) {
		$magic_quotes = get_magic_quotes_gpc();
	}

	if (is_array($value)) {
		if ($magic_quotes) {
			$new_value = array();
			foreach ($value as $key => $val) {
				$new_value[is_string($key) ? stripslashes($key) : $key] = dms_stripslashes_deep_keys($val);
			}
			$value = $new_value;
			unset($new_value);
		} else {
			$value = array_map('dms_stripslashes_deep_keys', $value);
		}
	} elseif (is_object($value)) {
		$vars = get_object_vars($value);
		foreach ($vars as $key => $data) {
			$value->{$key} = dms_stripslashes_deep_keys($data);
		}
	} elseif (is_string($value)) {
		$value = stripslashes($value);
	}

	return $value;
}

/**
 * Add slashes to values, and to keys if magic_quotes_gpc = On
 */
function dms_addslashes_deep_keys($value)
{
	static $magic_quotes = null;
	if ($magic_quotes === null) {
		$magic_quotes = get_magic_quotes_gpc();
	}

	if (is_array($value)) {
		if ($magic_quotes) {
			$new_value = array();
			foreach ($value as $key => $value) {
				$new_value[is_string($key) ? addslashes($key) : $key] = dms_addslashes_deep_keys($value);
			}
			$value = $new_value;
			unset($new_value);
		} else {
			$value = array_map('dms_addslashes_deep_keys', $value);
		}
	} elseif (is_object($value)) {
		$vars = get_object_vars($value);
		foreach ($vars as $key => $data) {
			$value->{$key} = dms_addslashes_deep_keys($data);
		}
	} elseif (is_string($value)) {
		$value = addslashes($value);
	}

	return $value;
}

/**
 * Check if current screen pass/match give rules
 *
 * @param array $rules Rules for current screen
 *
 * @return bool
 */
function dms_current_screen_match(array $rules)
{
	$available_options = array(
		'action'      => true,
		'base'        => true,
		'id'          => true,
		'is_network'  => true,
		'is_user'     => true,
		'parent_base' => true,
		'parent_file' => true,
		'post_type'   => true,
		'taxonomy'    => true,
	);

	if (empty($rules)) {
		return true;
	}

	$rules = array_merge(
		array(
			'exclude' => array(), // array of arrays or array with keys from $available_options
			'only'    => array(), // same as in 'exclude'
		),
		$rules
	);

	if (empty($rules['exclude']) && empty($rules['only'])) {
		return true;
	}

	global $current_screen;

	if (gettype($current_screen) != 'object') {
		return false;
	}

	// check if current screen passes the "only" rules
	do {
		$only = $rules['only'];

		if (empty($only)) {
			break;
		}

		if (!isset($only[0])) { // if not array of arrays
			$only = array($only);
		}

		$found_one = false;
		$counter   = 0;
		foreach ($only as $rule) {
			if (!count($rule)) {
				continue;
			}

			$match = true;

			foreach ($rule as $r_key => $r_val) {
				if (!isset($available_options[$r_key])) {
					continue;
				}

				if (gettype($r_val) != 'array') {
					$r_val = array($r_val);
				}

				$counter++;

				if (!in_array($current_screen->{$r_key}, $r_val)) {
					$match = false;
					break;
				}
			}

			if ($match) {
				$found_one = true;
				break;
			}
		}

		if (!$found_one && $counter) {
			return false;
		}
	} while (false);

	// check if current screen passes the "exclude" rules
	do {
		$exclude = $rules['exclude'];

		if (empty($exclude)) {
			break;
		}

		if (!isset($exclude[0])) { // if not array of arrays
			$exclude = array($exclude);
		}

		foreach ($exclude as $rule) {
			if (!count($rule)) {
				continue;
			}

			$match   = true;
			$counter = 0;

			foreach ($rule as $r_key => $r_val) {
				if (!isset($available_options[$r_key])) {
					continue;
				}

				if (gettype($r_val) != 'array') {
					$r_val = array($r_val);
				}

				$counter++;

				if (!in_array($current_screen->{$r_key}, $r_val)) {
					$match = false;
					break;
				}
			}

			if ($match && $counter) {
				return false;
			}
		}
	} while (false);

	return true;
}

/**
 * Search relative path in child then in parent theme directory and return URI
 *
 * @param  string $rel_path '/some/path_to_dir' or '/some/path_to_file.php'
 *
 * @return string URI
 */
function dms_locate_theme_path_uri($rel_path)
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
 *
 * @param  string $rel_path '/some/path_to_dir' or '/some/path_to_file.php'
 *
 * @return string URI
 */
function dms_locate_theme_path($rel_path)
{
	if (is_child_theme() && file_exists(get_stylesheet_directory() . $rel_path)) {
		return get_stylesheet_directory() . $rel_path;
	} elseif (file_exists(get_template_directory() . $rel_path)) {
		return get_template_directory() . $rel_path;
	} else {
		return false;
	}
}


if (!function_exists('dms_render_view')) :

	function dms_render_view($file_path, $view_variables = array(), $return = true)
	{

		if (!is_file($file_path)) {
			return '';
		}

		extract($view_variables, EXTR_REFS);
		unset($view_variables);

		if ($return) {
			ob_start();
			require $file_path;

			return ob_get_clean();
		} else {
			require $file_path;
		}

		return '';
	}
endif;

/**
 * Safe load variables from an file
 * Use this function to not include files directly and to not give access to current context variables (like $this)
 *
 * @param string $file_path
 * @param array $_extract_variables Extract these from file array('variable_name' => 'default_value')
 * @param array $_set_variables Set these to be available in file (like variables in view)
 *
 * @return array
 */
function dms_get_variables_from_file($file_path, array $_extract_variables, array $_set_variables = array())
{
	extract($_set_variables, EXTR_REFS);
	unset($_set_variables);

	require $file_path;

	foreach ($_extract_variables as $variable_name => $default_value) {
		if (isset($$variable_name)) {
			$_extract_variables[$variable_name] = $$variable_name;
		}
	}

	return $_extract_variables;
}

/**
 * Use this function to not include files directly and to not give access to current context variables (like $this)
 *
 * @param string $file_path
 * @param bool $once
 *
 * @return bool If was included or not
 */
function dms_include_file_isolated($file_path, $once = false)
{
	if (file_exists($file_path)) {
		if ((bool) $once) {
			include_once $file_path;
		} else {
			include $file_path;
		}

		return true;
	} else {
		return false;
	}
}

/**
 * Extract only input options (without containers)
 *
 * @param array $options
 *
 * @return array {option_id => option}
 */
function dms_extract_only_options(array $options)
{
	$collected = array();

	dms_collect_options($collected, $options);

	return $collected;
}

/**
 * Collect correct options from the first level of the array and group them
 *
 * @param array $collected Will be filled with found correct options
 * @param array $options
 *
 * @deprecated
 * It is deprecated since 2.4 because container types were added and there can be any type of containers
 * but this function is hardcoded only for tab,box,group.
 * Use dms_collect_options()
 */
function dms_collect_first_level_options(&$collected, &$options)
{
	if (empty($options)) {
		return;
	}

	if (empty($collected)) {
		$collected['tabs'] =
			$collected['boxes'] =
			$collected['groups'] =
			$collected['options'] =
			$collected['groups_and_options'] =
			$collected['all'] = array();
	}

	foreach ($options as $option_id => &$option) {
		if (isset($option['options'])) {
			// this is container for other options

			switch ($option['type']) {
				case 'tab':
					$collected['tabs'][$option_id] = &$option;
					break;
				case 'box':
					$collected['boxes'][$option_id] = &$option;
					break;
				case 'group':
					$collected['groups'][$option_id]             = &$option;
					$collected['groups_and_options'][$option_id] = &$option;
					break;
				default:
					trigger_error('Invalid option container type: ' . $option['type'], E_USER_WARNING);
					continue 2;
			}

			$collected['all'][$option['type'] . ':~:' . $option_id] = array(
				'type'   => $option['type'],
				'id'     => $option_id,
				'option' => &$option,
			);
		} elseif (
			is_int($option_id)
			&&
			is_array($option)
			&&
			/**
			 * make sure the array key was generated automatically
			 * and it's not an associative array with numeric keys created like this: $options[1] = array();
			 */
			isset($options[0])
		) {
			/**
			 * Array "without key" containing options.
			 *
			 * This happens when options are returned into array from a function:
			 * $options = array(
			 *  'foo' => array('type' => 'text'),
			 *  'bar' => array('type' => 'textarea'),
			 *
			 *  // this is our case
			 *  // go inside this array and extract the options as they are on the same array level
			 *  array(
			 *      'hello' => array('type' => 'text'),
			 *  ),
			 *
			 *  // there can be any nested arrays
			 *  array(
			 *      array(
			 *          array(
			 *              'h1' => array('type' => 'text'),
			 *          ),
			 *      ),
			 *  ),
			 * )
			 */
			dms_collect_first_level_options($collected, $option);
		} elseif (isset($option['type'])) {
			// simple option, last possible level in options array
			$collected['options'][$option_id]            = &$option;
			$collected['groups_and_options'][$option_id] = &$option;

			$collected['all']['option' . ':~:' . $option_id] = array(
				'type'   => 'option',
				'id'     => $option_id,
				'option' => &$option,
			);
		} else {
			trigger_error('Invalid option: ' . $option_id, E_USER_WARNING);
		}
	}
	unset($option);
}

/**
 * @param array $result
 * @param array $options
 * @param array $settings
 * @param array $_recursion_data (private) for internal use
 */
function dms_collect_options(&$result, &$options, $settings = array(), $_recursion_data = array())
{
	static $default_settings = array(
		/**
		 * @type bool Wrap the result/collected options in arrays will useful info
		 *
		 * If true:
		 * $result = array(
		 *   '(container|option):{id}' => array(
		 *      'id' => '{id}',
		 *      'level' => int, // from which nested level this option is
		 *      'group' => 'container|option',
		 *      'option' => array(...),
		 *   )
		 * )
		 *
		 * If false:
		 * $result = array(
		 *   '{id}' => array(...),
		 *   // Warning: There can be options and containers with the same id (array key will be replaced)
		 * )
		 */
		'info_wrapper'          => false,
		/**
		 * @type int Nested options level limit. For e.g. use 1 to collect only first level. 0 is for unlimited.
		 */
		'limit_level'           => 0,
		/**
		 * @type false|array('option-type', ...) Empty array will skip all types
		 */
		'limit_option_types'    => false,
		/**
		 * @type false|array('container-type', ...) Empty array will skip all types
		 */
		'limit_container_types' => array(),
		/**
		 * @type int Limit the number of options that will be collected
		 */
		'limit'                 => 0,
		/**
		 * @type callable Executed on each collected option
		 * @since 2.6.0
		 */
		'callback'              => null,
	);

	static $access_key = null;

	if (empty($options)) {
		return;
	}

	if (empty($_recursion_data)) {
		if (is_null($access_key)) {
			$access_key = new DMS_Access_Key('dms_collect_options');
		}

		$settings = array_merge($default_settings, $settings);

		$_recursion_data = array(
			'level'      => 1,
			'access_key' => $access_key,
			// todo: maybe add 'parent' => array('id' => '{id}', 'type' => 'container|option') ?
		);
	} elseif (!(isset($_recursion_data['access_key'])
		&&
		($_recursion_data['access_key'] instanceof DMS_Access_Key)
		&&
		($_recursion_data['access_key']->get_key() === 'dms_collect_options'))) {
		trigger_error('Call not allowed', E_USER_ERROR);
	}

	if (
		$settings['limit_level']
		&&
		$_recursion_data['level'] > $settings['limit_level']
	) {
		return;
	}

	foreach ($options as $option_id => &$option) {
		if (isset($option['options'])) { // this is a container
			do {
				if (
					is_array($settings['limit_container_types'])
					&&
					(
						// Customizer options can contain options with not existing or empty $option['type']
						empty($option['type'])
						||
						!in_array($option['type'], $settings['limit_container_types']))
				) {
					break;
				}

				if (
					$settings['limit']
					&&
					count($result) >= $settings['limit']
				) {
					return;
				}

				if ($settings['info_wrapper']) {
					$result['container:' . $option_id] = array(
						'group'  => 'container',
						'id'     => $option_id,
						'option' => &$option,
						'level'  => $_recursion_data['level'],
					);
				} else {
					$result[$option_id] = &$option;
				}

				if ($settings['callback']) {
					call_user_func_array(
						$settings['callback'],
						array(
							array(
								'group'  => 'container',
								'id'     => $option_id,
								'option' => &$option,
							)
						)
					);
				}
			} while (false);

			dms_collect_options(
				$result,
				$option['options'],
				$settings,
				array_merge($_recursion_data, array('level' => $_recursion_data['level'] + 1))
			);
		} elseif (
			is_int($option_id)
			&&
			is_array($option)
			&&
			/**
			 * make sure the array key was generated automatically
			 * and it's not an associative array with numeric keys created like this: $options[1] = array();
			 */
			isset($options[0])
		) {
			/**
			 * Array "without key" containing options.
			 *
			 * This happens when options are returned into array from a function:
			 * $options = array(
			 *  'foo' => array('type' => 'text'),
			 *  'bar' => array('type' => 'textarea'),
			 *
			 *  // this is our case
			 *  // go inside this array and extract the options as they are on the same array level
			 *  array(
			 *      'hello' => array('type' => 'text'),
			 *  ),
			 *
			 *  // there can be any nested arrays
			 *  array(
			 *      array(
			 *          array(
			 *              'h1' => array('type' => 'text'),
			 *          ),
			 *      ),
			 *  ),
			 * )
			 */
			dms_collect_options($result, $option, $settings, $_recursion_data);
		} elseif (isset($option['type'])) { // option
			if (
				is_array($settings['limit_option_types'])
				&&
				!in_array($option['type'], $settings['limit_option_types'])
			) {
				continue;
			}

			if (
				$settings['limit']
				&&
				count($result) >= $settings['limit']
			) {
				return;
			}

			if ($settings['info_wrapper']) {
				$result['option:' . $option_id] = array(
					'group'  => 'option',
					'id'     => $option_id,
					'option' => &$option,
					'level'  => $_recursion_data['level'],
				);
			} else {
				$result[$option_id] = &$option;
			}

			if ($settings['callback']) {
				call_user_func_array(
					$settings['callback'],
					array(
						array(
							'group'  => 'option',
							'id'     => $option_id,
							'option' => &$option,
						)
					)
				);
			}
		} else {
			trigger_error('Invalid option: ' . $option_id, E_USER_WARNING);
		}
	}
}

/**
 * Get correct values from input (POST) for given options
 * This values can be saved in db then replaced with $option['value'] for each option
 *
 * @param array $options
 * @param array $input_array
 *
 * @return array Values
 */
function dms_get_options_values_from_input(array $options, $input_array = null)
{
	if (!is_array($input_array)) {
		$input_array = DMS_Request::POST(dms()->backend->get_options_name_attr_prefix());
	}

	$values = array();

	$maybe_new_values = apply_filters(
		'dms:get_options_values_from_input:before',
		null,
		$options,
		$input_array
	);

	if ($maybe_new_values) {
		return $maybe_new_values;
	}

	foreach (dms_extract_only_options($options) as $id => $option) {
		$values[$id] = dms()->backend->option_type($option['type'])->get_value_from_input(
			$option,
			isset($input_array[$id]) ? $input_array[$id] : null
		);

		if (is_null($values[$id])) {
			// do not save null values
			unset($values[$id]);
		}
	}

	return $values;
}

/**
 * @param $attr_name
 * @param bool $set_mode
 *
 * @return mixed
 */
function dms_html_attr_name_to_array_multi_key($attr_name, $set_mode = false)
{
	if ($set_mode) {
		/**
		 * The key will be used to set value in array
		 * 'hello[world][]' -> 'hello/world/'
		 * $array['hello']['world'][] = $value;
		 */
		$attr_name = str_replace('[]', '/', $attr_name);
	} else {
		/**
		 * The key will be used to get value from array
		 * 'hello[world][]' -> 'hello/world'
		 * $value = $array['hello']['world'];
		 */
		$attr_name = str_replace('[]', '', $attr_name);
	}

	$attr_name = str_replace('][', '/', $attr_name);
	$attr_name = str_replace('[', '/', $attr_name);
	$attr_name = str_replace(']', '', $attr_name);

	return $attr_name;
}

/**
 * Used when getting some option value from serialized array saved in a custom place
 * and that option is unreachable for standard WordPress filters by other plugins
 * For e.g. that option cannot be translated by plugins, so we pass its value through this function and do the fixes
 *
 * @param $value
 *
 * @return array
 */
function dms_prepare_option_value($value)
{
	if (empty($value)) {
		return $value;
	}

	if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) {
		$value = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($value);
	}

	return $value;
}

/**
 * This function is used in 'save_post' action
 *
 * Used to check if current post save is a regular "Save" button press
 * not a revision, auto-save or something else
 *
 * @param $post_id
 *
 * @return bool
 *
 * @deprecated
 * save_post action happens also happens on Preview, Revision, Auto-save Restore, ...
 * the verifications in this function simplifies too much the save process,
 * the developers should study and understand better how it works
 * and handle different save cases in their scripts using wp functions
 */
function dms_is_real_post_save($post_id)
{
	return !(wp_is_post_revision($post_id)
		|| wp_is_post_autosave($post_id)
		|| (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		|| (defined('DOING_AJAX') && DOING_AJAX)
		|| empty($_POST)
		|| empty($_POST['post_ID'])
		|| $_POST['post_ID'] != $post_id);
}

/**
 * @return Array with Google fonts
 */
function dms_get_google_fonts()
{
	$cache_key = 'dms_google_fonts';

	try {
		return DMS_Cache::get($cache_key);
	} catch (DMS_Cache_Not_Found_Exception $e) {
		$g_fonts   = json_decode(dms_get_google_fonts_v2(), true);
		$old_fonts = include(dirname(__FILE__) . '/dms-google-fonts.json.php');
		$fonts     = array();

		foreach ($g_fonts['items'] as $font) {
			$fonts[$font['family']] = array(
				'family'   => $font['family'],
				'variants' => $font['variants'],
				'position' => isset($old_fonts[$font['family']])
					? $old_fonts[$font['family']]['position']
					: 99999
			);
		}

		$fonts = apply_filters('dms_google_fonts', $fonts);

		DMS_Cache::set($cache_key, $fonts);

		return $fonts;
	}
}

/**
 * @return string JSON encoded array with Google fonts
 */
function dms_get_google_fonts_v2()
{
	$saved_data = get_option('dms_google_fonts', false);

	$ttl        = 7 * DAY_IN_SECONDS;

	if (
		false === $saved_data
		||
		($saved_data['last_update'] + $ttl < time())
	) {
		$response = wp_remote_get(apply_filters(
			'dms_googleapis_webfonts_url',
			'http://dev.finesttheme.com/devmonsta/fonts/webfonts.json'
		));
		$body     = wp_remote_retrieve_body($response);

		if (
			200 === wp_remote_retrieve_response_code($response)
			&&
			!is_wp_error($body) && !empty($body)
		) {
			update_option(
				'dms_google_fonts',
				array(
					'last_update' => time(),
					'fonts'       => $body
				),
				false
			);

			return $body;
		} else {
			if (empty($saved_data['fonts'])) {
				$saved_data['fonts'] = json_encode(array('items' => array()));
			}

			update_option(
				'dms_google_fonts',
				array(
					'last_update' => time() - $ttl + MINUTE_IN_SECONDS,
					'fonts'       => $saved_data['fonts']
				),
				false
			);
		}
	}

	return $saved_data['fonts'];
}

/**
 * @return string Current url
 */
function dms_current_url()
{
	static $url = null;
	if ($url === null) {
		if (is_multisite() && !(defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL)) {
			switch_to_blog(1);
			$url = get_option('home');
			restore_current_blog();
		} else {
			$url = get_option('home');
		}

		//Remove the "//" before the domain name
		$url = ltrim(dms_get_url_without_scheme($url), '/');

		//Remove the ulr subdirectory in case it has one
		$split = explode('/', $url);

		//Remove end slash
		$url = rtrim($split[0], '/');

		$url .= '/' . ltrim(dms_akg('REQUEST_URI', $_SERVER, ''), '/');
		$url = set_url_scheme('//' . $url); // https fix
	}

	return $url;
}

function dms_is_valid_domain_name($domain_name)
{
	return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) // valid chars check
		&& preg_match("/^.{1,253}$/", $domain_name) // overall length check
		&& preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)); // length of each label
}

/**
 * Use this id do not want to enter every time same last two parameters
 * Info: Cannot use default parameters because in php 5.2 encoding is not UTF-8 by default
 *
 * @param string $string
 *
 * @return string
 */
function dms_htmlspecialchars($string)
{
	return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Check if current user has one capability from the given list
 *
 * @param array $capabilities list of capabilities to check
 * @param mixed $default_value
 *
 * @return string|bool|mixed
 *  Return first capability that user can.
 *  Else, return default value if it is not null, else return first capability from list.
 *  Use default value false to check if user can some of the capabilities
 */
function dms_current_user_can($capabilities, $default_value = null)
{
	if (is_user_logged_in()) {
		foreach ($capabilities as $capability) {
			if (current_user_can($capability)) {
				return $capability;
			}
		}
	}

	return ($default_value !== null ? dms_call($default_value) : array_shift($capabilities));
}

/**
 * Convert number of seconds to 'X {units}'
 *
 * E.g. 123 => '2 minutes'
 * then you can use this string how you want, for e.g. append ' ago' => '2 minutes ago'
 *
 * @param int $seconds
 *
 * @return string
 */
function dms_human_time($seconds)
{
	static $translations = null;
	if ($translations === null) {
		$translations = array(
			'year'  => __('year', 'dms'),
			'years' => __('years', 'dms'),

			'month'  => __('month', 'dms'),
			'months' => __('months', 'dms'),

			'week'  => __('week', 'dms'),
			'weeks' => __('weeks', 'dms'),

			'day'  => __('day', 'dms'),
			'days' => __('days', 'dms'),

			'hour'  => __('hour', 'dms'),
			'hours' => __('hours', 'dms'),

			'minute'  => __('minute', 'dms'),
			'minutes' => __('minutes', 'dms'),

			'second'  => __('second', 'dms'),
			'seconds' => __('seconds', 'dms'),
		);
	}

	$tokens = array(
		31536000 => 'year',
		2592000  => 'month',
		604800   => 'week',
		86400    => 'day',
		3600     => 'hour',
		60       => 'minute',
		1        => 'second'
	);

	foreach ($tokens as $unit => $translation_key) {
		if ($seconds < $unit) {
			continue;
		}

		$number_of_units = floor($seconds / $unit);

		return $number_of_units . ' ' . $translations[$translation_key . ($number_of_units != 1 ? 's' : '')];
	}
}

/**
 * Convert bytes to human readable format
 *
 * @param integer $bytes Size in bytes to convert
 * @param integer $precision
 *
 * @return string
 * @since 2.4.17
 */
function dms_human_bytes($bytes, $precision = 2)
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

function dms_strlen($string)
{
	if (function_exists('mb_strlen')) {
		return mb_strlen($string, 'UTF-8');
	} else {
		return strlen($string);
	}
}

/**
 * If currently is a Post Edit page display/submit
 * @return bool
 */
function dms_is_post_edit()
{
	static $result = null;

	if ($result === null) {
		$result = false;

		if (is_admin()) {
			if (
				empty($_POST)
				&&
				isset($_GET['action'])
				&&
				$_GET['action'] === 'edit'
				&&
				isset($_GET['post'])
			) {
				// Display Edit Post page
				$result = true;
			} elseif (
				isset($_POST['action'])
				&&
				$_POST['action'] === 'editpost'
				&&
				isset($_POST['post_type'])
				&&
				isset($_POST['post_ID'])
				&&
				strpos(wp_get_referer(), 'action=edit') !== false
			) {
				// Submit Edit Post page
				$result = true;
			}
		}
	}

	return $result;
}

/**
 * @param string $dirname 'foo-bar'
 *
 * @return string 'Foo_Bar'
 */
function dms_dirname_to_classname($dirname)
{
	$class_name = explode('-', $dirname);
	$class_name = array_map('ucfirst', $class_name);
	$class_name = implode('_', $class_name);

	return $class_name;
}

/**
 * This function is a wrapper function that set correct width and height for iframes from wp_oembed_get() function
 *
 * @param $url
 * @param array $args
 *
 * @return bool|string
 */
function dms_oembed_get($url, $args = array())
{
	$html = wp_oembed_get($url, $args);

	if (!empty($args['width']) and !empty($args['height']) and class_exists('DOMDocument') and !empty($html)) {
		$dom_element = new DOMDocument();
		@$dom_element->loadHTML($html);

		if ($obj = $dom_element->getElementsByTagName('iframe')->item(0)) {
			$obj->setAttribute('width', $args['width']);
			$obj->setAttribute('height', $args['height']);
			//saveXml instead of SaveHTML for php version compatibility
			$html = $dom_element->saveXML($obj, LIBXML_NOEMPTYTAG);
		}
	}

	return $html;
}

function dms_secure_rand($length)
{
	if (function_exists('openssl_random_pseudo_bytes')) {
		$rnd = openssl_random_pseudo_bytes($length, $strong);
		if ($strong) {
			return $rnd;
		}
	}

	$sha = '';
	$rnd = '';

	if (file_exists('/dev/urandom')) {
		$fp = fopen('/dev/urandom', 'rb');
		if ($fp) {
			if (function_exists('stream_set_read_buffer')) {
				stream_set_read_buffer($fp, 0);
			}
			$sha = fread($fp, $length);
			fclose($fp);
		}
	}

	for ($i = 0; $i < $length; $i++) {
		$sha  = hash('sha256', $sha . mt_rand());
		$char = mt_rand(0, 62);
		$rnd .= chr(hexdec($sha[$char] . $sha[$char + 1]));
	}

	return $rnd;
}

/**
 * Try to make user friendly title from an id
 *
 * @param string $id 'hello-world'
 *
 * @return string 'Hello world'
 */
function dms_id_to_title($id)
{
	// mb_ucfirst()
	if (function_exists('mb_strtoupper') && function_exists('mb_substr') && function_exists('mb_strlen')) {
		$id = mb_strtoupper(mb_substr($id, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr(
			$id,
			1,
			mb_strlen($id, 'UTF-8'),
			'UTF-8'
		);
	} else {
		$id = strtoupper(substr($id, 0, 1)) . substr($id, 1, strlen($id));
	}

	return str_replace(array('_', '-'), ' ', $id);
}

/*
 * Return URI without scheme
 */
function dms_get_url_without_scheme($url)
{
	return preg_replace('/^[^:]+:\/\//', '//', $url);
}

/**
 * Try to find file path by its uri and read the file contents
 *
 * @param string $file_uri
 *
 * @return bool|string false or string - the file contents
 */
function dms_read_file_by_uri($file_uri)
{
	global $wp_filesystem;
	static $base = null;

	if ($base === null) {
		$base                     = array();
		$base['dir']              = WP_CONTENT_DIR;
		$base['uri']              = ltrim(content_url(), '/');
		$base['uri_prefix_regex'] = '/^' . preg_quote($base['uri'], '/') . '/';
	}

	$file_rel_path = preg_replace($base['uri_prefix_regex'], '', $file_uri);

	if ($base['uri'] === $file_rel_path) {
		// the file is not inside base dir
		return false;
	}

	$file_path = $base['dir'] . '/' . $file_rel_path;

	if (!file_exists($file_path)) {
		return false;
	}

	$data   = $wp_filesystem->get_contents($file_path);
	return $data;
	// return file_get_contents( $file_path );
}

/**
 * Make stylesheet contents (portable) independent of directory location
 * For e.g. replace relative paths 'url(img/bg.png)' with full paths 'url(http://site.com/assets/img/bg.png)'
 *
 * @param string $href 'http://.../style.css'
 * @param null|string $contents If not specified, will try to read from $href
 *
 * @return bool|string false - on failure; string - stylesheet contents
 */
function dms_make_stylesheet_portable($href, $contents = null)
{
	if (is_null($contents)) {
		$contents = dms_read_file_by_uri($href);

		if ($contents === false) {
			return false;
		}
	}

	$dir_uri = dirname($href);

	/**
	 * Replace relative 'url(img/bg.png)'
	 * with full 'url(http://site.com/assets/img/bg.png)'
	 *
	 * Do not touch if url starts with:
	 * - 'https://'
	 * - 'http://'
	 * - '/' (also matches '//')
	 * - '#' (for css property: "behavior: url(#behaveBinObject)")
	 * - 'data:'
	 */
	$contents = preg_replace(
		'/url\s*\((?!\s*[\'"]?(?:\/|data\:|\#|(?:https?:)?\/\/))\s*([\'"])?/',
		'url($1' . $dir_uri . '/',
		$contents
	);

	return $contents;
}

/**
 * Return all images sizes register by add_image_size() merged with
 * WordPress default image sizes.
 * @link https://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
 *
 * @param string $size
 *
 * @return array|bool
 */
function dms_get_image_sizes($size = '')
{
	global $_wp_additional_image_sizes;

	$sizes                        = array();
	$get_intermediate_image_sizes = get_intermediate_image_sizes();

	// Create the full array with sizes and crop info
	foreach ($get_intermediate_image_sizes as $_size) {
		if (in_array($_size, array('thumbnail', 'medium', 'large'))) {
			$sizes[$_size]['width']  = get_option($_size . '_size_w');
			$sizes[$_size]['height'] = get_option($_size . '_size_h');
			$sizes[$_size]['crop']   = (bool) get_option($_size . '_crop');
		} elseif (isset($_wp_additional_image_sizes[$_size])) {
			$sizes[$_size] = array(
				'width'  => $_wp_additional_image_sizes[$_size]['width'],
				'height' => $_wp_additional_image_sizes[$_size]['height'],
				'crop'   => $_wp_additional_image_sizes[$_size]['crop']
			);
		}
	}

	// Get only 1 size if found
	if ($size) {
		if (isset($sizes[$size])) {
			return $sizes[$size];
		} else {
			return false;
		}
	}

	return $sizes;
}

/**
 * @param string $icon A string that is meant to be an icon (an image, a font icon class, or something else)
 * @param array Additional attributes
 *
 * @return string
 */
function dms_string_to_icon_html($icon, array $attributes = array())
{
	if (
		preg_match('/\.(png|jpg|jpeg|gif|svg|webp)$/', $icon)
		||
		preg_match('/^data:image\//', $icon)
	) {
		// http://.../image.png
		$tag  = 'img';
		$attr = array(
			'src' => $icon,
			'alt' => 'icon',
		);
	} elseif (preg_match('/^[a-zA-Z0-9\-_ ]+$/', $icon)) {
		// 'font-icon font-icon-class'
		$tag  = 'span';
		$attr = array(
			'class' => trim($icon),
		);
	} else {
		// can't detect. maybe it's raw html '<span ...'
		return $icon;
	}

	foreach ($attributes as $attr_name => $attr_val) {
		if (isset($attr[$attr_name])) {
			if ($attr_name === 'class') {
				$attr[$attr_name] .= ' ' . $attr_val;
			} else {
				// ignore. do not overwrite already set attributes
			}
		} else {
			$attr[$attr_name] = (string) $attr_val;
		}
	}

	return dms_html_tag($tag, $attr);
}

/**
 * @return string|null
 * @since 2.4.10
 */
function dms_get_json_last_error_message()
{
	switch (function_exists('json_last_error') ? json_last_error() : -1) {
		case JSON_ERROR_NONE:
			return null; // __('No errors', 'dms');
			break;
		case JSON_ERROR_DEPTH:
			return __('Maximum stack depth exceeded', 'dms');
			break;
		case JSON_ERROR_STATE_MISMATCH:
			return __('Underflow or the modes mismatch', 'dms');
			break;
		case JSON_ERROR_CTRL_CHAR:
			return __('Unexpected control character found', 'dms');
			break;
		case JSON_ERROR_SYNTAX:
			return __('Syntax error, malformed JSON', 'dms');
			break;
		case JSON_ERROR_UTF8:
			return __('Malformed UTF-8 characters, possibly incorrectly encoded', 'dms');
			break;
		default:
			return __('Unknown error', 'dms');
			break;
	}
}

/**
 * Return mime_types by file extension ex : input : array( 'png', 'jpg', 'jpeg' ) => output : array( 'image/jpeg' ).
 *
 * @param array $type
 *
 * @return array
 */
function dms_get_mime_type_by_ext($type = array())
{
	$result = array();

	foreach (wp_get_mime_types() as $key => $mime_type) {
		$types = explode('|', $key);
		foreach ($type as $item) {
			if (in_array($item, $types) && !in_array($mime_type, $result)) {
				$result[] = $mime_type;
			}
		}
	}

	return $result;
}

/**
 * Return types from file extensions ex : input array( 'png', 'jpg', 'zip' ) => output : array( 'image', 'archive' ).
 *
 * @see wp_ext2type() function.
 *
 * @param array $ext_array
 *
 * @return array
 */
function dms_multi_ext2type($ext_array = array())
{
	$result = array();

	foreach ($ext_array as $ext) {
		if (!in_array($type = wp_ext2type($ext), $result)) {
			$result[] = $type;
		}
	}

	return $result;
}

if (!function_exists('dms_resize')) {
	function dms_resize($url, $width = false, $height = false, $crop = false)
	{
		$dms_resize = DMS_Resize::getInstance();
		$response  = $dms_resize->process($url, $width, $height, $crop);

		return (!is_wp_error($response) && !empty($response['src'])) ? $response['src'] : $url;
	}
}


function dms_get_remote_file_content($url)
{
	$curlSession = curl_init();
	curl_setopt($curlSession, CURLOPT_URL, $url);
	curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($curlSession);
	curl_close($curlSession);
	return $data;
}

/**
 * dms_get_path_url( dirname(__FILE__) .'/test.css' ) --> http://site.url/path/to/test.css
 *
 * @param string $path
 *
 * @return string|null
 * @since 2.6.11
 */
function dms_get_path_url($path)
{
	try {
		$paths_to_urls = DMS_Cache::get($cache_key = 'dms:paths_to_urls');
	} catch (DMS_Cache_Not_Found_Exception $e) {
		$wp_upload_dir = wp_upload_dir();

		$paths_to_urls = array(
			dms_fix_path(WP_PLUGIN_DIR)             => plugins_url(),
			dms_fix_path(get_theme_root())          => get_theme_root_uri(),
			dms_fix_path($wp_upload_dir['basedir']) => $wp_upload_dir['baseurl'],
		);

		if (is_multisite() && WPMU_PLUGIN_DIR) {
			$paths_to_urls[dms_fix_path(WPMU_PLUGIN_DIR)] = WPMU_PLUGIN_URL;
		}

		DMS_Cache::set($cache_key, $paths_to_urls);
	}

	$path = dms_fix_path($path);

	foreach ($paths_to_urls as $_path => $_url) {
		if (preg_match($regex = '/^' . preg_quote($_path, '/') . '($|\/)/', $path)) {
			return $_url . '/' . preg_replace($regex, '', $path);
		}
	}

	return null;
}

/**
 * @param string|array $callback Callback function
 * @param array $args Callback arguments
 * @param bool $cache Whenever you want to cache the function value after it's first call or not
 * Recommend when the function call may require many resources or time (database requests) , or the value is small
 * Not recommended using on very large values
 *
 * @return DMS_Callback
 *
 * @since 2.6.14
 */
function dms_callback($callback, array $args = array(), $cache = true)
{
	return new DMS_Callback($callback, $args, $cache);
}

/**
 * In the value is instance of DMS_Callback class then it is executed and returns the callback value
 * In other case function returns the provided value
 *
 * @param mixed|DMS_Callback $value
 *
 * @return mixed
 *
 * @since 2.6.14
 */
function dms_call($value)
{
	if (!dms_is_callback($value)) {
		return $value;
	}

	return (is_object($value) && get_class($value) == 'Closure')
		? $value()
		: $value->execute();
}

/**
 * Check is the current value is instance of DMS_Callback class
 *
 * @param mixed $value
 *
 * @return bool
 */
function dms_is_callback($value)
{
	return $value instanceof DMS_Callback || (is_object($value) && get_class($value) == 'Closure');
}

/**
 * Check for command line interface
 *
 * @return bool
 * @since 2.6.16
 */
function dms_is_cli()
{
	return (php_sapi_name() === 'cli') && defined('WP_CLI');
}


function dms_widgets_export()
{

	$available_widgets = dms_available_widgets();
	$widget_instances = array();
	// Loop widgets.
	foreach ($available_widgets as $widget_data) {
		// Get all instances for this ID base.
		$instances = get_option('widget_' . $widget_data['id_base']);
		// Have instances.
		if (!empty($instances)) {
			// Loop instances.
			foreach ($instances as $instance_id => $instance_data) {
				// Key is ID (not _multiwidget).
				if (is_numeric($instance_id)) {
					$unique_instance_id = $widget_data['id_base'] . '-' . $instance_id;
					$widget_instances[$unique_instance_id] = $instance_data;
				}
			}
		}
	}
	// Gather sidebars with their widget instances.
	$sidebars_widgets = get_option('sidebars_widgets');
	$sidebars_widget_instances = array();
	foreach ($sidebars_widgets as $sidebar_id => $widget_ids) {
		// Skip inactive widgets.
		if ('wp_inactive_widgets' === $sidebar_id) {
			continue;
		}
		if (!is_array($widget_ids) || empty($widget_ids)) {
			continue;
		}
		foreach ($widget_ids as $widget_id) {
			if (isset($widget_instances[$widget_id])) {
				$sidebars_widget_instances[$sidebar_id][$widget_id] = $widget_instances[$widget_id];
			}
		}
	}

	// Filter pre-encoded data.
	$data = apply_filters('dms_unencoded_export_data', $sidebars_widget_instances);

	// Encode the data for file contents.
	$encoded_data = wp_json_encode($data);

	// Return contents.
	return apply_filters('dms_generate_export_data', $encoded_data);
}

function dms_available_widgets()
{

	global $wp_registered_widget_controls;

	$widget_controls = $wp_registered_widget_controls;

	$available_widgets = array();

	foreach ($widget_controls as $widget) {

		// No duplicates.
		if (!empty($widget['id_base']) && !isset($available_widgets[$widget['id_base']])) {
			$available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
			$available_widgets[$widget['id_base']]['name']    = $widget['name'];
		}
	}

	return $available_widgets;
}

function dms_widgets_import_data($data)
{

	global $wp_registered_sidebars;
	if (empty($data) || !is_object($data)) {

		wp_die(
			esc_html__('Import data could not be read. Please try a different file.', 'dms'),
			'',
			array(
				'back_link' => true,
			)
		);
	}
	$available_widgets = dms_available_widgets();
	$widget_instances = array();
	foreach ($available_widgets as $widget_data) {
		$widget_instances[$widget_data['id_base']] = get_option('widget_' . $widget_data['id_base']);
	}

	// Begin results.
	$results = array();

	// Loop import data's sidebars.
	foreach ($data as $sidebar_id => $widgets) {

		// Skip inactive widgets (should not be in export file).
		if ('wp_inactive_widgets' === $sidebar_id) {
			continue;
		}

		// Check if sidebar is available on this site.
		// Otherwise add widgets to inactive, and say so.
		if (isset($wp_registered_sidebars[$sidebar_id])) {
			$sidebar_available    = true;
			$use_sidebar_id       = $sidebar_id;
			$sidebar_message_type = 'success';
			$sidebar_message      = '';
		} else {
			$sidebar_available    = false;
			$use_sidebar_id       = 'wp_inactive_widgets';
			$sidebar_message_type = 'error';
			$sidebar_message      = esc_html__('Widget area does not exist in theme (using Inactive)', 'dms');
		}

		$results[$sidebar_id]['name']         = !empty($wp_registered_sidebars[$sidebar_id]['name']) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id;
		$results[$sidebar_id]['message_type'] = $sidebar_message_type;
		$results[$sidebar_id]['message']      = $sidebar_message;
		$results[$sidebar_id]['widgets']      = array();

		// Loop widgets.
		foreach ($widgets as $widget_instance_id => $widget) {

			$fail = false;

			// Get id_base (remove -# from end) and instance ID number.
			$id_base            = preg_replace('/-[0-9]+$/', '', $widget_instance_id);
			$instance_id_number = str_replace($id_base . '-', '', $widget_instance_id);

			// Does site support this widget?
			if (!$fail && !isset($available_widgets[$id_base])) {
				$fail                = true;
				$widget_message_type = 'error';
				$widget_message = esc_html__('Site does not support widget', 'dms'); // Explain why widget not imported.
			}
			$widget = json_decode(wp_json_encode($widget), true);
			$widget = apply_filters('dms_widget_settings_array', $widget);

			if (!$fail && isset($widget_instances[$id_base])) {

				// Get existing widgets in this sidebar.
				$sidebars_widgets = get_option('sidebars_widgets');
				$sidebar_widgets = isset($sidebars_widgets[$use_sidebar_id]) ? $sidebars_widgets[$use_sidebar_id] : array(); // Check Inactive if that's where will go.

				// Loop widgets with ID base.
				$single_widget_instances = !empty($widget_instances[$id_base]) ? $widget_instances[$id_base] : array();
				foreach ($single_widget_instances as $check_id => $check_widget) {
					// Is widget in same sidebar and has identical settings?
					if (in_array("$id_base-$check_id", $sidebar_widgets, true) && (array) $widget === $check_widget) {
						$fail = true;
						$widget_message_type = 'warning';
						// Explain why widget not imported.
						$widget_message = esc_html__('Widget already exists', 'dms');
						break;
					}
				}
			}

			if (!$fail) {
				$single_widget_instances = get_option('widget_' . $id_base);
				$single_widget_instances = !empty($single_widget_instances) ? $single_widget_instances : array(
					'_multiwidget' => 1,
				);
				$single_widget_instances[] = $widget; // Add it.
				// Get the key it was given.
				end($single_widget_instances);
				$new_instance_id_number = key($single_widget_instances);
				if ('0' === strval($new_instance_id_number)) {
					$new_instance_id_number = 1;
					$single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
					unset($single_widget_instances[0]);
				}


				if (isset($single_widget_instances['_multiwidget'])) {
					$multiwidget = $single_widget_instances['_multiwidget'];
					unset($single_widget_instances['_multiwidget']);
					$single_widget_instances['_multiwidget'] = $multiwidget;
				}

				// Update option with new widget.
				update_option('widget_' . $id_base, $single_widget_instances);

				$sidebars_widgets = get_option('sidebars_widgets');
				if (!$sidebars_widgets) {
					$sidebars_widgets = array();
				}

				// Use ID number from new widget instance.
				$new_instance_id = $id_base . '-' . $new_instance_id_number;

				// Add new instance to sidebar.
				$sidebars_widgets[$use_sidebar_id][] = $new_instance_id;

				// Save the amended data.
				update_option('sidebars_widgets', $sidebars_widgets);

				// After widget import action.
				$after_widget_import = array(
					'sidebar'           => $use_sidebar_id,
					'sidebar_old'       => $sidebar_id,
					'widget'            => $widget,
					'widget_type'       => $id_base,
					'widget_id'         => $new_instance_id,
					'widget_id_old'     => $widget_instance_id,
					'widget_id_num'     => $new_instance_id_number,
					'widget_id_num_old' => $instance_id_number,
				);

				// Success message.
				if ($sidebar_available) {
					$widget_message_type = 'success';
					$widget_message      = esc_html__('Imported', 'dms');
				} else {
					$widget_message_type = 'warning';
					$widget_message      = esc_html__('Imported to Inactive', 'dms');
				}
			}

			// Result for widget instance
			$results[$sidebar_id]['widgets'][$widget_instance_id]['name'] = isset($available_widgets[$id_base]['name']) ? $available_widgets[$id_base]['name'] : $id_base; // Widget name or ID if name not available (not supported by site).
			$results[$sidebar_id]['widgets'][$widget_instance_id]['title']        = !empty($widget['title']) ? $widget['title'] : esc_html__('No Title', 'dms'); // Show "No Title" if widget instance is untitled.
			$results[$sidebar_id]['widgets'][$widget_instance_id]['message_type'] = $widget_message_type;
			$results[$sidebar_id]['widgets'][$widget_instance_id]['message']      = $widget_message;
		}
	}

	// Return results.
	return apply_filters('dms_widgets_import_results', $results);
}

// customizer 

function dms_customizer_settings_export($wp_customize)
{

	$core_options = array(
		'blogname',
		'blogdescription',
		'show_on_front',
		'page_on_front',
		'page_for_posts',
	);

	$theme		= get_stylesheet();
	$template	= get_template();
	$charset	= get_option('blog_charset');
	$mods		= get_theme_mods();
	$data		= array(
		'template'  => $template,
		'mods'	  => $mods ? $mods : array(),
		'options'	  => array()
	);

	// Get options from the Customizer API.
	$settings = $wp_customize->settings();

	foreach ($settings as $key => $setting) {

		if ('option' == $setting->type) {

			// Don't save widget data.
			if ('widget_' === substr(strtolower($key), 0, 7)) {
				continue;
			}

			// Don't save sidebar data.
			if ('sidebars_' === substr(strtolower($key), 0, 9)) {
				continue;
			}

			// Don't save core options.
			if (in_array($key, $core_options)) {
				continue;
			}

			$data['options'][$key] = $setting->value();
		}
	}

	$option_keys = apply_filters('dms_export_option_keys', array());

	foreach ($option_keys as $option_key) {
		$data['options'][$option_key] = get_option($option_key);
	}

	if (function_exists('wp_get_custom_css_post')) {
		$data['wp_css'] = wp_get_custom_css();
	}

	return $data;
}
