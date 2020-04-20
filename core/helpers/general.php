<?php if (!defined('DM')) {
	die('Forbidden');
}
// Useful functions

	/**
	 * print_r() alternative
	 *
	 * @param mixed $value Value to debug
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
	 */
	function dm_rand()
	{
		return md5(time() . '-' . uniqid(rand(), true) . '-' . mt_rand(1, 1000));
	}

	/**
	 * Search relative path in child then in parent theme directory and return URI
	 * @param  string $rel_path '/some/path_to_dir' or '/some/path_to_file.php'
	 * @return string URI
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * URI to the parent-theme/framework directory
	 *
	 * @param string $rel_path
	 *
	 * @return string
	 * @since 1.0.0
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
	
	/**
	 * Escape markup with allowed tags and attributs
	 * does not invalidate markup tags
	 * @param string $raw
	 * @return string
	 * @since 1.0.0
	 */
	function dm_kses($raw)
	{
		$allowed_tags = array(
			'a'	  => array(
					'class'	 => array(),
					'href'	 => array(),
					'rel'	 => array(),
					'title'	 => array(),
					'target' => array(),
					),
			'upload' => array(),
			'input' => [
						'value' => [],
						'type' => [],
						'size' => [],
						'name' => [],
						'checked' => [],
						'placeholder' => [],
						'id' => [],
						'class' => [],
					],

			'select' => [
						'value' => [],
						'type' => [],
						'size' => [],
						'name' => [],
						'placeholder' => [],
						'id' => [],
						'class' => [],
						'option' => [
							'value' => [],
							'checked' => [],
						]
					],

			'textarea' => [
						'value' => [],
						'type' => [],
						'size' => [],
						'name' => [],
						'rows' => [],
						'cols' => [],

						'placeholder' => [],
						'id' => [],
						'class' => []
					],
			'abbr'	  => array(
						'title' => array(),
					),
			'b'		    => array(),
			'blockquote' => array(
							'cite' => array(),
						),
			'cite'							 => array(
				'title' => array(),
			),
			'code'							 => array(),
			'del'							 => array(
				'datetime'	 => array(),
				'title'		 => array(),
			),
			'dd'							 => array(),
			'div'							 => array(
				'id' => array(),
				'class'	 => array(),
				'title'	 => array(),
				'style'	 => array(),
				'data-attid' => array(),
			),
			'dl'							 => array(),
			'dt'							 => array(),
			'em'							 => array(),
			'h1'							 => array(
				'class' => array(),
			),
			'h2'							 => array(
				'class' => array(),
			),
			'h3'							 => array(
				'class' => array(),
			),
			'h4'							 => array(
				'class' => array(),
			),
			'h5'							 => array(
				'class' => array(),
			),
			'h6'							 => array(
				'class' => array(),
			),
			'i'								 => array(
				'class' => array(),
			),
			'img'							 => array(
				'id' => array(),
				'alt'	 => array(),
				'class'	 => array(),
				'height' => array(),
				'src'	 => array(),
				'width'	 => array(),
			),
			'li'							 => array(
				'class' => array(),
			),
			'ol'							 => array(
				'class' => array(),
			),
			'p'								 => array(
				'class' => array(),
			),
			'q'								 => array(
				'cite'	 => array(),
				'title'	 => array(),
			),
			'span'							 => array(
				'class'	 => array(),
				'title'	 => array(),
				'style'	 => array(),
			),
			'iframe'						 => array(
				'width'			 => array(),
				'height'		 => array(),
				'scrolling'		 => array(),
				'frameborder'	 => array(),
				'allow'			 => array(),
				'src'			 => array(),
			),
			'strike'						 => array(),
			'br'							 => array(
				'class' => array()
			),
			'strong'						 => array(),
			'data-wow-duration'				 => array(),
			'data-wow-delay'				 => array(),
			'data-wallpaper-options'		 => array(),
			'data-stellar-background-ratio'	 => array(),
			'ul'							 => array(
				'class' => array(),
			),
			'label'	=> array(
				'class' => array(),
				'for' => array(),
			)
		);

		if (function_exists('wp_kses')) { // WP is here
			return wp_kses($raw, $allowed_tags);
		} else {
			return $raw;
		}
	}

	/**
	 * Renders text into html surrounded with span tag
	 * @param string $text
	 * @return string
	 * @since 1.0.0
	 */
	function dm_kspan($text)
	{
		return str_replace(['{', '}'], ['<span>', '</span>'], dm_kses($text));
	}

	/**
	 * Returns html markup if not null
	 * Used for rendering html markup
	 *
	 * @param string $content
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function dm_render_markup($content){
		if($content == ""){
			return null;
		}
		return $content;
	}

		
	/**
	 * @return string Current url
	 * @since 1.0.0
	 */
	function dm_current_url()
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
			$url = ltrim(dm_get_url_without_scheme($url), '/');

			//Remove the ulr subdirectory in case it has one
			$split = explode('/', $url);

			//Remove end slash
			$url = rtrim($split[0], '/');

			$url .= '/' . ltrim(dm_array_key_get('REQUEST_URI', $_SERVER, ''), '/');
			$url = set_url_scheme('//' . $url); // https fix
		}

		return $url;
	}

	/*
	* Return URI without scheme
	 * @since 1.0.0
	*/
	function dm_get_url_without_scheme($url)
	{
		return preg_replace('/^[^:]+:\/\//', '//', $url);
	}

	/**
	 * Full path to the child-theme framework customizations directory
	 *
	 * @param string $rel_path
	 *
	 * @return null|string
	 * @since 1.0.0
	 */
	function dm_get_stylesheet_directory($rel_path = '')
	{
		if (is_child_theme()) {
			return get_stylesheet_directory() .
			 dm_get_customizations_dir_rel_path($rel_path);
		} else {
			// check is_child_theme() before using this function
			return null;
		}
	}

	/**
	 * URI to the child-theme framework customizations directory
	 *
	 * @param string $rel_path
	 *
	 * @return null|string
	 * @since 1.0.0
	 */
	function dm_get_stylesheet_directory_uri($rel_path = '')
	{
		if (is_child_theme()) {
			return get_stylesheet_directory_uri() .
			 dm_get_customizations_dir_rel_path($rel_path);
		} else {
			// check is_child_theme() before using this function
			return null;
		}
	}
	
	/**
	 * Relative path of the framework customizations directory
	 * @param string $append
	 * @return string
	 * @since 1.0.0
	 */
	function dm_get_customizations_dir_rel_path($append = '')
	{
		try {
			$dir = Dm_Cache::get($cache_key = 'dm_customizations_dir_rel_path');
		} catch (Dm_Cache_Not_Found_Exception $e) {
			Dm_Cache::set(
				$cache_key,
				$dir = apply_filters('dm_customizations_dir_rel_path', '/devmonsta')
			);
		}

		return $dir . $append;
	}

	/**
	 * Get a image alt value from the database.
	 * @param string $image_id
	 * @return string
	 * @since 1.0.0
	 */
	function dm_post_img_alt($image_id) {
		if ( !empty( $image_id ) ) {
		   $alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		   if ( !empty( $alt ) ) {
			  $alt = $alt;
		   } else {
			  $alt = get_the_title( $image_id );
		   }
		   return $alt;
		}
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
function dm_aku($keys, &$array_or_object, $keys_delimiter = '/')
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
			dm_aku($keys, $array_or_object->{$key_or_property});
		} else {
			dm_aku($keys, $array_or_object[$key_or_property]);
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
 * Set (or create if not exists) value for specified key in some array level
 *
 * @param string $keys 'a/b/c', or 'a/b/c/' equivalent to: $arr['a']['b']['c'][] = $val;
 * @param mixed $value
 * @param array|object $array_or_object
 * @param string $keys_delimiter
 *
 * @return array|object
 */
function dm_aks($keys, $value, &$array_or_object, $keys_delimiter = '/')
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
			dm_aks($keys, $value, $array_or_object->{$key_or_property});
		} else {
			dm_aks($keys, $value, $array_or_object[$key_or_property]);
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
 * Recursively find a key's value in array
 *
 * @param string $keys 'a/b/c'
 * @param array|object $array_or_object
 * @param null|mixed $default_value
 * @param string $keys_delimiter
 *
 * @return null|mixed
 */
function dm_akg($keys, $array_or_object, $default_value = null, $keys_delimiter = '/')
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
			return dm_akg($keys, $array_or_object->{$key_or_property}, $default_value);
		} else {
			return dm_akg($keys, $array_or_object[$key_or_property], $default_value);
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
 * @param string|array $callback Callback function
 * @param array $args Callback arguments
 * @param bool $cache Whenever you want to cache the function value after it's first call or not
 * Recommend when the function call may require many resources or time (database requests) , or the value is small
 * Not recommended using on very large values
 *
 * @return DM_Callback
 *
 * @since 2.6.14
 */
function dm_callback($callback, array $args = array(), $cache = true)
{
	return new Dm_Callback($callback, $args, $cache);
}
	/**
	 * Full path to the parent-theme framework customizations directory
	 *
	 * @param string $rel_path
	 *
	 * @return string
	 */
	function dm_get_template_customizations_directory($rel_path = '')
	{
		try {
			$dir = DM_Cache::get($cache_key = 'dm_template_customizations_dir');
		} catch (DM_Cache_Not_Found_Exception $e) {
			DM_Cache::set(
				$cache_key,
				$dir = get_template_directory() . dm_get_framework_customizations_dir_rel_path()
			);
		}

		return $dir . $rel_path;
	}
	/** Child theme related functions */ {
	/**
	 * Full path to the child-theme framework customizations directory
	 *
	 * @param string $rel_path
	 *
	 * @return null|string
	 */
	function dm_get_stylesheet_customizations_directory($rel_path = '')
	{
		if (is_child_theme()) {
			return get_stylesheet_directory() . dm_get_framework_customizations_dir_rel_path($rel_path);
		} else {
			// check is_child_theme() before using this function
			return null;
		}
	}
	/**
 * Relative path of the framework customizations directory
 *
 * @param string $append
 *
 * @return string
 */
function dm_get_framework_customizations_dir_rel_path($append = '')
{
	try {
		$dir = DM_Cache::get($cache_key = 'dm_customizations_dir_rel_path');
	} catch (DM_Cache_Not_Found_Exception $e) {
		DM_Cache::set(
			$cache_key,
			$dir = apply_filters('dm_framework_customizations_dir_rel_path', '/framework-customizations')
		);
	}

			return $dir . $append;
		}
}
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
function dm_get_variables_from_file($file_path, array $_extract_variables, array $_set_variables = array())
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
 * Generate html tag
 *
 * @param string $tag Tag name
 * @param array $attr Tag attributes
 * @param bool|string $end Append closing tag. Also accepts body content
 *
 * @return string The tag's html
 */
function dm_html_tag($tag, $attr = array(), $end = false)
{
	$html = '<' . $tag . ' ' . dm_attr_to_html($attr);

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
function dm_attr_to_html(array $attr_array)
{
	$html_attr = '';

	foreach ($attr_array as $attr_name => $attr_val) {
		if ($attr_val === false) {
			continue;
		}

		$html_attr .= $attr_name . '="' . dm_htmlspecialchars($attr_val) . '" ';
	}

	return $html_attr;
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
function dm_get_options_values_from_input(array $options, $input_array = null)
{
	if (!is_array($input_array)) {
		$input_array = DM_Request::POST(dm()->backend->get_options_name_attr_prefix());
	}

	$values = array();

	$maybe_new_values = apply_filters(
		'dm:get_options_values_from_input:before',
		null,
		$options,
		$input_array
	);

	if ($maybe_new_values) {
		return $maybe_new_values;
	}

	foreach (dm_extract_only_options($options) as $id => $option) {
		$values[$id] = dm()->backend->option_type($option['type'])->get_value_from_input(
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
function dm_html_attr_name_to_array_multi_key($attr_name, $set_mode = false)
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
 * @param array $result
 * @param array $options
 * @param array $settings
 * @param array $_recursion_data (private) for internal use
 */
function dm_collect_options(&$result, &$options, $settings = array(), $_recursion_data = array())
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
			$access_key = new dm_Access_Key('dm_collect_options');
		}

		$settings = array_merge($default_settings, $settings);

		$_recursion_data = array(
			'level'      => 1,
			'access_key' => $access_key,
			// todo: maybe add 'parent' => array('id' => '{id}', 'type' => 'container|option') ?
		);
	} elseif (!(isset($_recursion_data['access_key'])
		&&
		($_recursion_data['access_key'] instanceof dm_Access_Key)
		&&
		($_recursion_data['access_key']->get_key() === 'dm_collect_options'))) {
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

			dm_collect_options(
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
			dm_collect_options($result, $option, $settings, $_recursion_data);
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
function dm_unique_increment()
{
	static $i = 0;

	return ++$i;
}

function render($content){
	if($content == ""){
		return null;
	}
	return $content;
}
if (!function_exists('dm_render_view')) :

	function dm_render_view($file_path, $view_variables = array(), $return = true)
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
 * Try to make user friendly title from an id
 *
 * @param string $id 'hello-world'
 *
 * @return string 'Hello world'
 */
function dm_id_to_title($id)
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
/**
 * Strip slashes from values, and from keys if magic_quotes_gpc = On
 */
function dm_stripslashes_deep_keys($value)
{
	static $magic_quotes = null;
	if ($magic_quotes === null) {
		$magic_quotes = get_magic_quotes_gpc();
	}

	if (is_array($value)) {
		if ($magic_quotes) {
			$new_value = array();
			foreach ($value as $key => $val) {
				$new_value[is_string($key) ? stripslashes($key) : $key] = dm_stripslashes_deep_keys($val);
			}
			$value = $new_value;
			unset($new_value);
		} else {
			$value = array_map('dm_stripslashes_deep_keys', $value);
		}
	} elseif (is_object($value)) {
		$vars = get_object_vars($value);
		foreach ($vars as $key => $data) {
			$value->{$key} = dm_stripslashes_deep_keys($data);
		}
	} elseif (is_string($value)) {
		$value = stripslashes($value);
	}

	return $value;
}

/**
 * Add slashes to values, and to keys if magic_quotes_gpc = On
 */
function dm_addslashes_deep_keys($value)
{
	static $magic_quotes = null;
	if ($magic_quotes === null) {
		$magic_quotes = get_magic_quotes_gpc();
	}

	if (is_array($value)) {
		if ($magic_quotes) {
			$new_value = array();
			foreach ($value as $key => $value) {
				$new_value[is_string($key) ? addslashes($key) : $key] = dm_addslashes_deep_keys($value);
			}
			$value = $new_value;
			unset($new_value);
		} else {
			$value = array_map('dm_addslashes_deep_keys', $value);
		}
	} elseif (is_object($value)) {
		$vars = get_object_vars($value);
		foreach ($vars as $key => $data) {
			$value->{$key} = dm_addslashes_deep_keys($data);
		}
	} elseif (is_string($value)) {
		$value = addslashes($value);
	}

	return $value;
}