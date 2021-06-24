<?php

if ( !defined( 'DEVM' ) ) {
    die( 'Forbidden' );
}

/**
 * print_r() alternative
 *
 * @param mixed $value Value to debug
 * @since 1.0.0
 */
function devm_print( $value ) {
    static $first_time = true;

    if ( $first_time ) {
        ob_start();
        echo '<style type="text/css">
			div.devm_print_r {
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

			div.devm_print_r pre {
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

			div.devm_print_r_group {
				background: #f1f1f1;
				margin: 10px 30px;
				padding: 1px;
				border-radius: 5px;
				position: relative;
				z-index: 11110;
			}

			div.devm_print_r_group div.devm_print_r {
				margin: 9px;
				border-width: 0;
			}
			</style>';
        echo str_replace( ['  ', "\n"], '', ob_get_clean() );

        $first_time = false;
    }

    if ( func_num_args() == 1 ) {
        echo '<div class="devm_print_r"><pre>';
        echo devm_htmlspecialchars( Devm_Dumper::dump( $value ) );
        echo '</pre></div>';
    } else {
        echo '<div class="devm_print_r_group">';

        foreach ( func_get_args() as $param ) {
            devm_print( $param );
        }

        echo '</div>';
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
function devm_htmlspecialchars( $string ) {
    return htmlspecialchars( $string, ENT_QUOTES, 'UTF-8' );
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
function devm_array_key_get( $keys, $array_or_object, $default_value = null, $keys_delimiter = '/' ) {

    if ( !is_array( $keys ) ) {
        $keys = explode( $keys_delimiter, (string) $keys );
    }

    $array_or_object = devm_call( $array_or_object );

    $key_or_property = array_shift( $keys );

    if ( $key_or_property === null ) {
        return devm_call( $default_value );
    }

    $is_object = is_object( $array_or_object );

    if ( $is_object ) {

        if ( !property_exists( $array_or_object, $key_or_property ) ) {
            return devm_call( $default_value );
        }

    } else {

        if ( !is_array( $array_or_object ) || !array_key_exists( $key_or_property, $array_or_object ) ) {
            return devm_call( $default_value );
        }

    }

    if ( isset( $keys[0] ) ) {

        // not used count() for performance reasons
        if ( $is_object ) {
            return devm_array_key_get( $keys, $array_or_object->{$key_or_property}, $default_value );
        } else {
            return devm_array_key_get( $keys, $array_or_object[$key_or_property], $default_value );
        }

    } else {
        if ( $is_object ) {
            return $array_or_object->{$key_or_property};
        } else {
            return $array_or_object[$key_or_property];
        }

    }

}

/**
 * Alias for devm_print
 *
 * @see devm_print()
 * @since 1.0.0
 */
if ( !function_exists( 'debug' ) ) {
    function debug() {
        call_user_func_array( 'devm_print', func_get_args() );
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
function devm_array_key_set( $keys, $value, &$array_or_object, $keys_delimiter = '/' ) {
    if ( !is_array( $keys ) ) {
        $keys = explode( $keys_delimiter, (string) $keys );
    }

    $key_or_property = array_shift( $keys );
    if ( $key_or_property === null ) {
        return $array_or_object;
    }

    $is_object = is_object( $array_or_object );

    if ( $is_object ) {
        if (
            !property_exists( $array_or_object, $key_or_property )
            || !( is_array( $array_or_object->{$key_or_property} ) || is_object( $array_or_object->{$key_or_property} ) )
        ) {
            if ( $key_or_property === '' ) {
                // this happens when use 'empty keys' like: abc/d/e////i/j//foo/
                trigger_error( 'Cannot push value to object like in array ($arr[] = $val)', E_USER_WARNING );
            } else {
                $array_or_object->{$key_or_property}

                = [];
            }

        }

    } else {

        if ( !is_array( $array_or_object ) ) {
            $array_or_object = [];
        }

        if (
            !array_key_exists(
                $key_or_property,
                $array_or_object
            ) || !is_array( $array_or_object[$key_or_property] )
        ) {

            if ( $key_or_property === '' ) {
                // this happens when use 'empty keys' like: abc.d.e....i.j..foo.
                $array_or_object[] = [];

                // get auto created key (last)
                end( $array_or_object );
                $key_or_property = key( $array_or_object );
            } else {
                $array_or_object[$key_or_property] = [];
            }

        }

    }

    if ( isset( $keys[0] ) ) {

        // not used count() for performance reasons
        if ( $is_object ) {
            devm_array_key_set( $keys, $value, $array_or_object->{$key_or_property} );
        } else {
            devm_array_key_set( $keys, $value, $array_or_object[$key_or_property] );
        }

    } else {
        if ( $is_object ) {
            $array_or_object->{$key_or_property}

            = $value;
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
function devm_array_key_unset( $keys, &$array_or_object, $keys_delimiter = '/' ) {
    if ( !is_array( $keys ) ) {
        $keys = explode( $keys_delimiter, (string) $keys );
    }

    $key_or_property = array_shift( $keys );
    if ( $key_or_property === null || $key_or_property === '' ) {
        return $array_or_object;
    }

    $is_object = is_object( $array_or_object );

    if ( $is_object ) {
        if ( !property_exists( $array_or_object, $key_or_property ) ) {
            return $array_or_object;
        }

    } else {
        if ( !is_array( $array_or_object ) || !array_key_exists( $key_or_property, $array_or_object ) ) {
            return $array_or_object;
        }

    }

    if ( isset( $keys[0] ) ) {

        // not used count() for performance reasons
        if ( $is_object ) {
            devm_array_key_unset( $keys, $array_or_object->{$key_or_property} );
        } else {
            devm_array_key_unset( $keys, $array_or_object[$key_or_property] );
        }

    } else {
        if ( $is_object ) {
            unset( $array_or_object->{$key_or_property} );
        } else {
            unset( $array_or_object[$key_or_property] );
        }

    }

    return $array_or_object;
}

/**
 * If the value is instance of Devm_Callback class then it is executed and returns the callback value
 * In other case function returns the provided value
 *
 * @param mixed|Devm_Callback $value
 *
 * @return mixed
 *
 * @since 1.0.0
 */
function devm_call( $value ) {
    if ( !devm_is_callback( $value ) ) {
        return $value;
    }

    return ( is_object( $value ) && get_class( $value ) == 'Closure' )
    ? $value()
    : $value->execute();
}

/**
 * Check if the current value is instance of Devm_Callback class
 *
 * @param mixed $value
 *
 * @return bool
 * @since 1.0.0
 */
function devm_is_callback( $value ) {
    return $value instanceof Devm_Callback || ( is_object( $value ) && get_class( $value ) == 'Closure' );
}

/**
 * Convert bytes to human readable format
 * @param integer $bytes Size in bytes to convert
 * @param integer $precision
 * @return string
 * @since 1.0.0
 */
function devm_human_bytes( $bytes, $precision = 2 ) {
    $kilobyte = 1024;
    $megabyte = $kilobyte * 1024;
    $gigabyte = $megabyte * 1024;
    $terabyte = $gigabyte * 1024;

    if (  ( $bytes >= 0 ) && ( $bytes < $kilobyte ) ) {
        return $bytes . ' B';
    } elseif (  ( $bytes >= $kilobyte ) && ( $bytes < $megabyte ) ) {
        return round( $bytes / $kilobyte, $precision ) . ' KB';
    } elseif (  ( $bytes >= $megabyte ) && ( $bytes < $gigabyte ) ) {
        return round( $bytes / $megabyte, $precision ) . ' MB';
    } elseif (  ( $bytes >= $gigabyte ) && ( $bytes < $terabyte ) ) {
        return round( $bytes / $gigabyte, $precision ) . ' GB';
    } elseif ( $bytes >= $terabyte ) {
        return round( $bytes / $terabyte, $precision ) . ' TB';
    } else {
        return $bytes . ' B';
    }

}

/**
 * Generate random unique md5
 * @since 1.0.0
 */
function devm_rand() {
    return md5( time() . '-' . uniqid( rand(), true ) . '-' . mt_rand( 1, 1000 ) );
}

/**
 * Search relative path in child then in parent theme directory and return URI
 * @param string $rel_path '/some/path_to_dir' or '/some/path_to_file.php'
 * @return string URI
 * @since 1.0.0
 */
function devm_theme_path_uri( $rel_path ) {
    if ( is_child_theme() && file_exists( get_stylesheet_directory() . $rel_path ) ) {
        return get_stylesheet_directory_uri() . $rel_path;
    } elseif ( file_exists( get_template_directory() . $rel_path ) ) {
        return get_template_directory_uri() . $rel_path;
    } else {
        return 'about:blank#theme-file-not-found:' . $rel_path;
    }

}

/**
 * Search relative path in child then in parent theme directory and return full path
 * @param string $rel_path '/some/path_to_dir' or '/some/path_to_file.php'
 * @return string URI
 * @since 1.0.0
 */
function devm_theme_path( $rel_path ) {
    if ( is_child_theme() && file_exists( get_stylesheet_directory() . $rel_path ) ) {
        return get_stylesheet_directory() . $rel_path;
    } elseif ( file_exists( get_template_directory() . $rel_path ) ) {
        return get_template_directory() . $rel_path;
    } else {
        return false;
    }

}

/**
 * Convert to Unix style directory separators
 * @since 1.0.0
 */
function devm_fix_path( $path ) {
    $windows_network_path = isset( $_SERVER['windir'] ) && in_array(
        substr( $path, 0, 2 ),
        ['//', '\\\\'],
        true
    );
    $fixed_path = untrailingslashit( str_replace( ['//', '\\'], ['/', '/'], $path ) );

    if ( empty( $fixed_path ) && !empty( $path ) ) {
        $fixed_path = '/';
    }

    if ( $windows_network_path ) {
        $fixed_path = '//' . ltrim( $fixed_path, '/' );
    }

    return $fixed_path;
}

/**
 * Full path to the parent-theme directory
 * @param string $rel_path
 * @return string
 * @since 1.0.0
 */
function devm_get_framework_directory( $rel_path = '' ) {
    try {
        $dir = Devm_Cache::get( $cache_key = 'devm_framework_dir' );
    } catch ( Devm_Cache_Not_Found_Exception $e ) {
        Devm_Cache::set(
            $cache_key,
            $dir = apply_filters(
                'devm_framework_directory',
                devm_fix_path( dirname( dirname( __FILE__ ) ) ) // double dirname() to remove '/helpers', use parent dir
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
function devm_get_framework_directory_uri( $rel_path = '' ) {
    try {
        $uri = Devm_Cache::get( $cache_key = 'devm_framework_dir_uri' );
    } catch ( Devm_Cache_Not_Found_Exception $e ) {
        Devm_Cache::set(
            $cache_key,
            $uri = apply_filters(
                'devm_framework_directory_uri',
                ( $uri = devm_get_path_url( devm_get_framework_directory() ) ) ? $uri : get_template_directory_uri()
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
function devm_kses( $raw ) {
    $allowed_tags = [
        'a'                             => [
            'class'  => [],
            'href'   => [],
            'rel'    => [],
            'title'  => [],
            'target' => [],
        ],
        'upload'                        => [],
        'input'                         => [
            'value'       => [],
            'type'        => [],
            'size'        => [],
            'name'        => [],
            'checked'     => [],
            'placeholder' => [],
            'id'          => [],
            'class'       => [],
        ],

        'select'                        => [
            'value'       => [],
            'type'        => [],
            'size'        => [],
            'name'        => [],
            'placeholder' => [],
            'id'          => [],
            'class'       => [],
            'option'      => [
                'value'   => [],
                'checked' => [],
            ],
        ],

        'textarea'                      => [
            'value'       => [],
            'type'        => [],
            'size'        => [],
            'name'        => [],
            'rows'        => [],
            'cols'        => [],

            'placeholder' => [],
            'id'          => [],
            'class'       => [],
        ],
        'abbr'                          => [
            'title' => [],
        ],
        'b'                             => [],
        'blockquote'                    => [
            'cite' => [],
        ],
        'cite'                          => [
            'title' => [],
        ],
        'code'                          => [],
        'del'                           => [
            'datetime' => [],
            'title'    => [],
        ],
        'dd'                            => [],
        'div'                           => [
            'id'         => [],
            'class'      => [],
            'title'      => [],
            'style'      => [],
            'data-attid' => [],
        ],
        'dl'                            => [],
        'dt'                            => [],
        'em'                            => [],
        'h1'                            => [
            'class' => [],
        ],
        'h2'                            => [
            'class' => [],
        ],
        'h3'                            => [
            'class' => [],
        ],
        'h4'                            => [
            'class' => [],
        ],
        'h5'                            => [
            'class' => [],
        ],
        'h6'                            => [
            'class' => [],
        ],
        'i'                             => [
            'class' => [],
        ],
        'img'                           => [
            'id'     => [],
            'alt'    => [],
            'class'  => [],
            'height' => [],
            'src'    => [],
            'width'  => [],
        ],
        'li'                            => [
            'class' => [],
        ],
        'ol'                            => [
            'class' => [],
        ],
        'p'                             => [
            'class' => [],
        ],
        'q'                             => [
            'cite'  => [],
            'title' => [],
        ],
        'span'                          => [
            'class' => [],
            'title' => [],
            'style' => [],
        ],
        'iframe'                        => [
            'width'       => [],
            'height'      => [],
            'scrolling'   => [],
            'frameborder' => [],
            'allow'       => [],
            'src'         => [],
        ],
        'strike'                        => [],
        'br'                            => [
            'class' => [],
        ],
        'strong'                        => [],
        'data-wow-duration'             => [],
        'data-wow-delay'                => [],
        'data-wallpaper-options'        => [],
        'data-stellar-background-ratio' => [],
        'ul'                            => [
            'class' => [],
        ],
        'label'                         => [
            'class' => [],
            'for'   => [],
        ],
    ];

    if ( function_exists( 'wp_kses' ) ) { // WP is here
        return wp_kses( $raw, $allowed_tags );
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
function devm_kspan( $text ) {
    return str_replace( ['{', '}'], ['<span>', '</span>'], devm_kses( $text ) );
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
function devm_render_markup( $content ) {

    if ( $content == "" ) {
        return null;
    }

    return $content;
}

/**
 * @return string Current url
 * @since 1.0.0
 */
function devm_current_url() {
    static $url = null;

    if ( $url === null ) {

        if ( is_multisite() && !( defined( 'SUBDOMAIN_INSTALL' ) && SUBDOMAIN_INSTALL ) ) {
            switch_to_blog( 1 );
            $url = get_option( 'home' );
            restore_current_blog();
        } else {
            $url = get_option( 'home' );
        }

        //Remove the "//" before the domain name
        $url = ltrim( devm_get_url_without_scheme( $url ), '/' );

        //Remove the ulr subdirectory in case it has one
        $split = explode( '/', $url );

        //Remove end slash
        $url = rtrim( $split[0], '/' );

        $url .= '/' . ltrim( devm_array_key_get( 'REQUEST_URI', $_SERVER, '' ), '/' );
        $url = set_url_scheme( '//' . $url ); // https fix
    }

    return $url;
}

/*
 * Return URI without scheme
 * @since 1.0.0
 */
function devm_get_url_without_scheme( $url ) {
    return preg_replace( '/^[^:]+:\/\//', '//', $url );
}

/**
 * Full path to the child-theme framework customizations directory
 *
 * @param string $rel_path
 *
 * @return null|string
 * @since 1.0.0
 */
function devm_get_stylesheet_directory( $rel_path = '' ) {

    if ( is_child_theme() ) {
        return get_stylesheet_directory() .
        devm_get_customizations_dir_rel_path( $rel_path );
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
function devm_get_stylesheet_directory_uri( $rel_path = '' ) {

    if ( is_child_theme() ) {
        return get_stylesheet_directory_uri() .
        devm_get_customizations_dir_rel_path( $rel_path );
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
function devm_get_customizations_dir_rel_path( $append = '' ) {
    try {
        $dir = Devm_Cache::get( $cache_key = 'devm_customizations_dir_rel_path' );
    } catch ( Devm_Cache_Not_Found_Exception $e ) {
        Devm_Cache::set(
            $cache_key,
            $dir = apply_filters( 'devm_customizations_dir_rel_path', '/devmonsta' )
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
function devm_post_img_alt( $image_id ) {

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
 * @param string|array $callback Callback function
 * @param array $args Callback arguments
 * @param bool $cache Whenever you want to cache the function value after it's first call or not
 * Recommend when the function call may require many resources or time (database requests) , or the value is small
 * Not recommended using on very large values
 *
 * @return DEVM_Callback
 *
 * @since 2.6.14
 */
function devm_callback( $callback, array $args = [], $cache = true ) {
    return new Devm_Callback( $callback, $args, $cache );
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
function devm_html_tag( $tag, $attr = [], $end = false ) {
    $html = '<' . $tag . ' ' . devm_attr_to_html( $attr );

    if ( $end === true ) {
        $html .= '></' . $tag . '>';
    } elseif ( $end === false ) {
        $html .= '/>';
    } else {
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
function devm_attr_to_html( array $attr_array ) {
    $html_attr = '';

    foreach ( $attr_array as $attr_name => $attr_val ) {

        if ( $attr_val === false ) {
            continue;
        }

        $html_attr .= $attr_name . '="' . devm_htmlspecialchars( $attr_val ) . '" ';
    }

    return $html_attr;
}

/**
 * Strip slashes from values, and from keys if magic_quotes_gpc = On
 */
function devm_stripslashes_deep_keys( $value ) {
    static $magic_quotes = null;

    if ( $magic_quotes === null ) {
        $magic_quotes = get_magic_quotes_gpc();
    }

    if ( is_array( $value ) ) {

        if ( $magic_quotes ) {
            $new_value = [];

            foreach ( $value as $key => $val ) {
                $new_value[is_string( $key ) ? stripslashes( $key ) : $key] = devm_stripslashes_deep_keys( $val );
            }

            $value = $new_value;
            unset( $new_value );
        } else {
            $value = array_map( 'devm_stripslashes_deep_keys', $value );
        }

    } elseif ( is_object( $value ) ) {
        $vars = get_object_vars( $value );

        foreach ( $vars as $key => $data ) {
            $value->{$key}

            = devm_stripslashes_deep_keys( $data );
        }

    } elseif ( is_string( $value ) ) {
        $value = stripslashes( $value );
    }

    return $value;
}

/**
 * This function is a wrapper function that set correct width and height for iframes from wp_oembed_get() function
 *
 * @param $url
 * @param array $args
 *
 * @return bool|string
 */
function devm_oembed_get( $url, $args = [] ) {
    $html = wp_oembed_get( $url, $args );

    if ( !empty( $args['width'] ) and !empty( $args['height'] ) and class_exists( 'DOMDocument' ) and !empty( $html ) ) {
        $dom_element = new DOMDocument();
        @$dom_element->loadHTML( $html );

        if ( $obj = $dom_element->getElementsByTagName( 'iframe' )->item( 0 ) ) {
            $obj->setAttribute( 'width', $args['width'] );
            $obj->setAttribute( 'height', $args['height'] );
            //saveXml instead of SaveHTML for php version compatibility
            $html = $dom_element->saveXML( $obj, LIBXML_NOEMPTYTAG );
        }

    }

    return $html;
}

/**
 * Return demo file path from theme
 *
 * @return void
 */ 
function devm_demo_file_path() {

    $demo_file = devm_theme_path( '/devmonsta/theme-demos.php' );
    return apply_filters( 'devm_demo_file_path', $demo_file );
}

/**
 * Returns demo array from theme directory
 *
 * @return void
 */
function devm_import_files() {
    $demo_data = [];
    $demo_file = devm_demo_file_path();
    if ( file_exists( $demo_file ) ) {
        require_once $demo_file;
    }
    $demo_data_array = apply_filters( 'devm_import_demo_files', $demo_data );

    return $demo_data_array;
}


/**
 * import widget data from XML file
 *
 * @param [type] $data
 * @return void
 */
function devm_widgets_import_data( $data ) {
    global $wp_registered_sidebars;

    if ( empty( $data ) || !is_object( $data ) ) {
        return new \WP_Error(
            'corrupted_widget_import_data',
            __( 'Error: Widget import data could not be read. Please try a different file.', 'devmonsta' )
        );
    }

    // Hook before import.
    do_action( 'devm/widget_importer_before_widgets_import' );
    $data = apply_filters( 'devm/before_widgets_import_data', $data );

    // Get all available widgets site supports.
    $available_widgets = devm_available_widgets();

    // Get all existing widget instances.
    $widget_instances = [];

    // Begin results.
    $results = [];

    // Loop import data's sidebars.
    foreach ( $data as $sidebar_id => $widgets ) {

        // Skip inactive widgets (should not be in export file).
        if ( 'wp_inactive_widgets' == $sidebar_id ) {
            continue;
        }

        // Check if sidebar is available on this site. Otherwise add widgets to inactive, and say so.
        if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
            $sidebar_available    = true;
            $use_sidebar_id       = $sidebar_id;
            $sidebar_message_type = 'success';
            $sidebar_message      = '';
        } else {
            $sidebar_available    = false;
            $use_sidebar_id       = 'wp_inactive_widgets'; // Add to inactive if sidebar does not exist in theme.
            $sidebar_message_type = 'error';
            $sidebar_message      = __( 'Sidebar does not exist in theme (moving widget to Inactive)', 'devmonsta' );
        }

                                                                                                                                                                   // Result for sidebar.
        $results[$sidebar_id]['name']         = !empty( $wp_registered_sidebars[$sidebar_id]['name'] ) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id; // Sidebar name if theme supports it; otherwise ID.
        $results[$sidebar_id]['message_type'] = $sidebar_message_type;
        $results[$sidebar_id]['message']      = $sidebar_message;
        $results[$sidebar_id]['widgets']      = [];

        // Loop widgets.
        foreach ( $widgets as $widget_instance_id => $widget ) {
            $fail = false;

            // Get id_base (remove -# from end) and instance ID number.
            $id_base            = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
            $instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );

            // Does site support this widget?
            if ( !$fail && !isset( $available_widgets[$id_base] ) ) {
                $fail                = true;
                $widget_message_type = 'error';
                $widget_message      = __( 'Site does not support widget', 'devmonsta' ); // Explain why widget not imported.
            }

            $widget = apply_filters( 'devm/widget_settings', $widget ); // Object.

            $widget = json_decode( json_encode( $widget ), true );

            $widget = apply_filters( 'devm/widget_settings_array', $widget );

            // Does widget with identical settings already exist in same sidebar?
            if ( !$fail && isset( $widget_instances[$id_base] ) ) {

                // Get existing widgets in this sidebar.
                $sidebars_widgets = get_option( 'sidebars_widgets' );
                $sidebar_widgets  = isset( $sidebars_widgets[$use_sidebar_id] ) ? $sidebars_widgets[$use_sidebar_id] : [];
                // Check Inactive if that's where will go.

                // Loop widgets with ID base.
                $single_widget_instances = !empty( $widget_instances[$id_base] ) ? $widget_instances[$id_base] : [];

                foreach ( $single_widget_instances as $check_id => $check_widget ) {

                    // Is widget in same sidebar and has identical settings?
                    if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {
                        $fail                = true;
                        $widget_message_type = 'warning';
                        $widget_message      = __( 'Widget already exists', 'devmonsta' ); // Explain why widget not imported.

                        break;
                    }

                }

            }

            // No failure.
            if ( !$fail ) {
                // Add widget instance.
                $single_widget_instances   = get_option( 'widget_' . $id_base );                                                         // All instances for that widget ID base, get fresh every time.
                $single_widget_instances   = !empty( $single_widget_instances ) ? $single_widget_instances : [ '_multiwidget' => 1 ]; // Start fresh if have to.
                $single_widget_instances[] = $widget;
                // Add it.

                // Get the key it was given.
                end( $single_widget_instances );
                $new_instance_id_number = key( $single_widget_instances );

                // If key is 0, make it 1.

                // When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it).
                if ( '0' === strval( $new_instance_id_number ) ) {
                    $new_instance_id_number                           = 1;
                    $single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
                    unset( $single_widget_instances[0] );
                }

                // Move _multiwidget to end of array for uniformity.    
                if ( isset( $single_widget_instances['_multiwidget'] ) ) {
                    $multiwidget = $single_widget_instances['_multiwidget'];
                    unset( $single_widget_instances['_multiwidget'] );
                    $single_widget_instances['_multiwidget'] = $multiwidget;
                }

                // Update option with new widget.
                update_option( 'widget_' . $id_base, $single_widget_instances );

                // Assign widget instance to sidebar.
                $sidebars_widgets = get_option( 'sidebars_widgets' );
                // Which sidebars have which widgets, get fresh every time.
                if ( !$sidebars_widgets ) {
                    $sidebars_widgets = [];
                }

                $new_instance_id                     = $id_base . '-' . $new_instance_id_number; // Use ID number from new widget instance.
                $sidebars_widgets[$use_sidebar_id][] = $new_instance_id;
                // Add new instance to sidebar.

                update_option( 'sidebars_widgets', $sidebars_widgets );
                // Save the amended data.

                // After widget import action.
                $after_widget_import = [
                    'sidebar'           => $use_sidebar_id,
                    'sidebar_old'       => $sidebar_id,
                    'widget'            => $widget,
                    'widget_type'       => $id_base,
                    'widget_id'         => $new_instance_id,
                    'widget_id_old'     => $widget_instance_id,
                    'widget_id_num'     => $new_instance_id_number,
                    'widget_id_num_old' => $instance_id_number,
                ];

                // Success message.
                if ( $sidebar_available ) {
                    $widget_message_type = 'success';
                    $widget_message      = __( 'Imported', 'devmonsta' );
                } else {
                    $widget_message_type = 'warning';
                    $widget_message      = __( 'Imported to Inactive', 'devmonsta' );
                }

            }
                                                                                                                                                                                   // Result for widget instance.
            $results[$sidebar_id]['widgets'][$widget_instance_id]['name']         = isset( $available_widgets[$id_base]['name'] ) ? $available_widgets[$id_base]['name'] : $id_base; // Widget name or ID if name not available (not supported by site).
            $results[$sidebar_id]['widgets'][$widget_instance_id]['title']        = !empty( $widget['title'] ) ? $widget['title'] : __( 'No Title', 'devmonsta' );                       // Show "No Title" if widget instance is untitled.
            $results[$sidebar_id]['widgets'][$widget_instance_id]['message_type'] = $widget_message_type;
            $results[$sidebar_id]['widgets'][$widget_instance_id]['message']      = $widget_message;

        }

    }

    // Hook after import.
    do_action( 'devm/widget_importer_after_widgets_import' );

    // Return results.
    return apply_filters( 'devm/widget_import_results', $results );
}

/**
 * devm_get_path_url( dirname(__FILE__) .'/test.css' ) -->
 *
 * @param string $path
 *
 * @return string|null
 * @since 2.6.11
 */
function devm_get_path_url( $path ) {
    try {
        $paths_to_urls = DEVM_Cache::get( $cache_key = 'devm:paths_to_urls' );
    } catch ( DEVM_Cache_Not_Found_Exception $e ) {
        $wp_upload_dir = wp_upload_dir();

        $paths_to_urls = [
            devm_fix_path( WP_PLUGIN_DIR )             => plugins_url(),
            devm_fix_path( get_theme_root() )          => get_theme_root_uri(),
            devm_fix_path( $wp_upload_dir['basedir'] ) => $wp_upload_dir['baseurl'],
        ];

        if ( is_multisite() && WPMU_PLUGIN_DIR ) {
            $paths_to_urls[devm_fix_path( WPMU_PLUGIN_DIR )] = WPMU_PLUGIN_URL;
        }

        DEVM_Cache::set( $cache_key, $paths_to_urls );
    }

    $path = devm_fix_path( $path );

    foreach ( $paths_to_urls as $_path => $_url ) {

        if ( preg_match( $regex = '/^' . preg_quote( $_path, '/' ) . '($|\/)/', $path ) ) {
            return $_url . '/' . preg_replace( $regex, '', $path );
        }

    }

    return null;
}

/**
 * Undocumented function
 *
 * @param [type] $post_id
 * @param [type] $option_id
 * @param [type] $default_value
 * @return void
 */
function devm_meta_option( $post_id, $option_id, $default_value = null ) {
    $prefix    = 'devmonsta_';
    $option_id = $prefix . $option_id;
    $post_id   = intval( $post_id );
    return get_post_meta( $post_id, $option_id, true );
}

function devm_taxonomy( $term_id, $key = '', $single = true ) {
    return get_term_meta( $term_id, $key, $single );
}

function devm_theme_option( $option_name, $default = false ) {

    if ( get_theme_mod( $option_name ) ) {

        if ( is_bool( get_theme_mod( $option_name ) ) ) {

            if ( get_theme_mod( $option_name ) ) {
                return (string) 'true';
            } else {
                return (string) 'false';
            }

        }

        return get_theme_mod( $option_name, $default );
    }

    return devm_theme_control_default_control( $option_name );
}

function devm_theme_media_option( $media_id ) {
    return wp_get_attachment_metadata( devm_theme_option( $media_id ) );
}

function devm_theme_control_default_control( $control_name ) {

    $control = devm_get_theme_control( $control_name );

    if ( $control != false ) {
        $default = '';

        if ( isset( $control['default'] ) ) {
            $default = $control['default'];
        }

        if ( isset( $control['value'] ) ) {
            $default = $control['value'];
        }

        return $default;
    }

}

function devm_get_theme_control( $control_name ) {
    $controls = devm_get_all_theme_controls();

    foreach ( $controls as $control ) {

        if ( $control['id'] == $control_name ) {
            return $control;
        }

    }

    return false;
}

function devm_get_all_theme_controls() {
    $controls = new Devmonsta\Options\Customizer\Controls();
    return $controls->get_controls();
}

function devm_backups_destination_directory() {
    $uploads = wp_upload_dir();
    return devm_fix_path( $uploads['basedir'] . "/elementor/css/" );
}

function devm_widgets_export() {

    $available_widgets = devm_available_widgets();
    $widget_instances  = [];

    // Loop widgets.
    foreach ( $available_widgets as $widget_data ) {
        // Get all instances for this ID base.
        $instances = get_option( 'widget_' . $widget_data['id_base'] );

        // Have instances.
        if ( !empty( $instances ) ) {

            // Loop instances.
            foreach ( $instances as $instance_id => $instance_data ) {

                // Key is ID (not _multiwidget).
                if ( is_numeric( $instance_id ) ) {
                    $unique_instance_id                    = $widget_data['id_base'] . '-' . $instance_id;
                    $widget_instances[$unique_instance_id] = $instance_data;
                }

            }

        }

    }

    // Gather sidebars with their widget instances.
    $sidebars_widgets          = get_option( 'sidebars_widgets' );
    $sidebars_widget_instances = [];

    foreach ( $sidebars_widgets as $sidebar_id => $widget_ids ) {

        // Skip inactive widgets.
        if ( 'wp_inactive_widgets' === $sidebar_id ) {
            continue;
        }

        if ( !is_array( $widget_ids ) || empty( $widget_ids ) ) {
            continue;
        }

        foreach ( $widget_ids as $widget_id ) {
            if ( isset( $widget_instances[$widget_id] ) ) {
                $sidebars_widget_instances[$sidebar_id][$widget_id] = $widget_instances[$widget_id];
            }

        }

    }

    // Filter pre-encoded data.
    $data = apply_filters( 'devm_unencoded_export_data', $sidebars_widget_instances );

    // Encode the data for file contents.
    $encoded_data = wp_json_encode( $data );

    // Return contents.
    return apply_filters( 'devm_generate_export_data', $encoded_data );
}

function devm_available_widgets() {
    global $wp_registered_widget_controls;
    $widget_controls   = $wp_registered_widget_controls;
    $available_widgets = [];

    foreach ( $widget_controls as $widget ) {

        // No duplicates.
        if ( !empty( $widget['id_base'] ) && !isset( $available_widgets[$widget['id_base']] ) ) {
            $available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
            $available_widgets[$widget['id_base']]['name']    = $widget['name'];
        }

    }

    return $available_widgets;
}

function devm_sanitize_data( $key, $value ) {
    $array_valued_controls = [
        'checkbox-multiple',
        'dimensions',
        'gradient',
        'icon',
        'multiselect',
        'typography',
    ]; // These controls value save as array and user don't have any scope to input invalid string

    $html_valued_controls = [
        'wp-editor',
    ]; // These controls value save as HTML

    $email_valued_controls = [
        'email',
    ];

    $control_type = devm_get_post_type( $key );

        // Sanitize email type controls
    if ( in_array( $control_type, $email_valued_controls ) ) {
        return sanitize_email( $value );
    }

    if ( in_array( $control_type, $array_valued_controls ) ) {
        return $value;
    }

    if ( in_array( $control_type, $html_valued_controls ) ) {
        return $value;
    }

    // Sanitize all controls string except array and HTML control.
    return sanitize_text_field( $value );
}

function devm_sanitize_taxonomy_data( $key, $value ) {
    $array_valued_controls = [
        'checkbox-multiple',
        'dimensions',
        'gradient',
        'icon',
        'multiselect',
        'typography',
    ]; // These controls value save as array and user don't have any scope to input invalid string

    $html_valued_controls = [
        'wp-editor',
    ]; // These controls value save as HTML

    $email_valued_controls = [
        'email',
    ];

    $control_type = devm_get_taxonomy_post_type( $key );

        // Sanitize email type controls
    if ( in_array( $control_type, $email_valued_controls ) ) {
        return sanitize_email( $value );
    }

    if ( in_array( $control_type, $array_valued_controls ) ) {
        return $value;
    }

    if ( in_array( $control_type, $html_valued_controls ) ) {
        return $value;
    }

    // Sanitize all controls string except array and HTML control.
    return sanitize_text_field( $value );
}

function devm_get_all_taxonomy_controls() {
    return get_option( 'devmonsta_all_taxonomy_controls' );
}

function devm_get_all_posteta_controls() {
    return get_option( 'devmonsta_all_potmeta_controls' );
}

function devm_get_post_type( $control_name ) {
    $all_controls = devm_get_all_posteta_controls();

    foreach ( $all_controls as $control ) {

        if ( 'devmonsta_' . $control['name'] == $control_name ) {
            return $control['type'];
        }

    }

}

function devm_get_taxonomy_post_type( $control_name ) {
    $all_controls = devm_get_all_taxonomy_controls();

    foreach ( $all_controls as $control ) {

        if ( isset( $control['name'] ) ) {

            if ( 'devmonsta_' . $control['name'] == $control_name ) {
                return $control['type'];
            }

        }

    }

}
