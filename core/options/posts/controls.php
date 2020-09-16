<?php

namespace Devmonsta\Options\Posts;

use Devmonsta\Libs\Posts as LibsPosts;

class Controls {

    public static function get_controls() {

        if ( !empty( self::get_post_files() ) ) {
            /** Include the file and stract the data  */
            $files = [];
            foreach ( self::get_post_files() as $file ) {

                require_once $file;
                $files[] = $file;
                /** Get the class name which is extended to @Devmonsta\Libs\Posts */
            }

            $post_file_class = [];

            foreach ( get_declared_classes() as $class ) {

                if ( is_subclass_of( $class, 'Devmonsta\Libs\Posts' ) ) {
                    $post_file_class[] = $class;
                }

            }

            /** Get all the properties defined in post file */

            $post_lib = new LibsPosts;

            foreach ( $post_file_class as $child_class ) {
                $post_file = new $child_class;

                if ( method_exists( $post_file, 'register_controls' ) ) {

                    $post_file->register_controls();

                }

            }

            /**
             *  Get all controls defined in theme
             */

            $all_controls = $post_lib->all_controls();

            return $all_controls;
        }

    }

    private static function get_post_files() {
        $files = [];

        foreach ( glob( get_template_directory() . '/devmonsta/options/posts/*.php' ) as $post_files ) {
            array_push( $files, $post_files );
        }

        return $files;
    }

    public static function get_control_type($control_name){
    	$all_controls = self::get_controls();
    	foreach($all_controls as $control){
    		if($control['name'] == $control_name)
    			return $control['type'];
	    }
    }

    public static function get_control_data($control_name){
	    $all_controls = self::get_controls();
	    foreach($all_controls as $control){
		    if($control['name'] == $control_name)
			    return $control;

	    }
    }

    public static function make_control($control_name){
    	$control_content = self::get_control_data($control_name);
	    $name = $control_content['name'];
	    unset( $control_content['name'] );
	    $control_content['name'] = $name;
	    $class_name              = explode( '-', $control_content['type'] );
	    $class_name              = array_map( 'ucfirst', $class_name );
	    $class_name              = implode( '', $class_name );
	    $control_class           = 'Devmonsta\Options\Posts\Controls\\' . $class_name . '\\' . $class_name;

	    if ( class_exists( $control_class ) ) {
		    $control = new $control_class( $control_content );
		    $control->init();
		    $control->render();
	    } else {
		    $file = plugin_dir_path( __FILE__ ) . 'controls/' . $control_content['type'] . '/' . $control_content['type'] . '.php';
		    if ( file_exists( $file ) ) {
			    include_once $file;
			    if ( class_exists( $control_class ) ) {
				    $control = new $control_class( $control_content );
				    $control->init();
				    $control->render();
			    }
		    }
	    }
    }

}
