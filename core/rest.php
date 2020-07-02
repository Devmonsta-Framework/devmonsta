<?php

namespace Devmonsta;

use Devmonsta\Libs\Posts as LibsPosts;
use Devmonsta\Traits\Singleton;

class Rest {

    use Singleton;

    public function init() {

        add_action( 'wp_ajax_devm_repeater_controls', [$this, 'devm_repeater_controls'] );
        add_action( 'wp_ajax_devm_get_control', [$this, 'devm_get_control'] );

    }

    public function devm_repeater_controls() {
        // $all_controls = $this->get_all_controls();
        print_r( $this->get_control( 'repeater', 'repeater_xxx' ) );
        wp_die();
    }

    public function devm_get_control() {
        $control = $this->get_control( $_POST['type'], $_POST['name'] );
        echo json_encode( $control );
        echo devm_render_markup( $this->get_control_markup( $control ) );
        wp_die();
    }

    public function get_all_controls() {

        if ( !empty( $this->get_post_files() ) ) {
            $files = [];

            foreach ( $this->get_post_files() as $file ) {

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

        return false;
    }

    public function get_control_markup( $control_content ) {
        $class_name    = explode( '-', $control_content['type'] );
        $class_name    = array_map( 'ucfirst', $class_name );
        $class_name    = implode( '', $class_name );
        $control_class = 'Devmonsta\Options\Posts\Controls\\' . $class_name . '\\' . $class_name;

        if ( class_exists( $control_class ) ) {

            $control = new $control_class( $control_content );

            $control->render();

        } else {

            $file = DEVMONSTA_DIR . '/core/options/posts/controls/' . $control_content['type'] . '/' . $control_content['type'] . '.php';

            if ( file_exists( $file ) ) {

                include_once $file;

                if ( class_exists( $control_class ) ) {

                    $control = new $control_class( $control_content );

                    $control->render();

                }

            }

        }

    }

    public function get_control( $type, $name ) {
        $all_controls   = $this->get_all_controls();
        $single_control = null;

        if ( $all_controls != false ) {

            foreach ( $all_controls as $control ) {

                if ( $control['type'] == $type && $control['name'] == $name ) {
                    $single_control = $control;
                }

            }

        }

        return $single_control;
    }

    public function get_post_files() {
        $files = [];

        foreach ( glob( get_template_directory() . '/devmonsta/options/posts/*.php' ) as $post_files ) {
            array_push( $files, $post_files );
        }

        return $files;
    }

}
