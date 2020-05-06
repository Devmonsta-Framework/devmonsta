<?php

/**
 * =================================
 *       Handel page , post and
 *      custom post type metabox
 * =================================
 */

namespace Devmonsta\Options\Posts;

use Devmonsta\Libs\Posts as LibsPosts;
use Devmonsta\Traits\Singleton;

class Posts {

    use Singleton;

    protected $data;

    public function init() {

        /** Check if post files exists in active theme directory */

        if ( !empty( $this->get_post_files() ) ) {

            /** Include the file and stract the data  */
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

            /** Get all the metaboxed that has been defined */

            $all_meta_box = $post_lib->all_boxes();

            /**
             *  Get all controls defined in theme
             */

            $all_controls = $post_lib->all_controls();

            /**
             *  Get Post type anem from the file name
             */

            foreach ( $files as $file_name ) {
                $post_type = basename( $file_name, ".php" );

                /** Create metabox */

                foreach ( $all_meta_box as $args ) {

                    if ( $post_type == $args['post_type'] ) {
                        $this->data = $args;

                        $this->load_enqueue( $all_controls );
                        $this->add_meta_box( $post_type, $args, $all_controls );
                    }

                }

            }

            // update_option('devmonsta_scripts',[]);

        }

        // add_action('add_meta_boxes', [$this, 'add']);
        add_action( 'save_post', [$this, 'save'] );

    }

    public function load_enqueue( $all_controls ) {

        foreach ( $all_controls as $control_content ) {

            if ( isset( $control_content['type'] ) ) {
                $class_name    = explode( '-', $control_content['type'] );
                $class_name    = array_map( 'ucfirst', $class_name );
                $class_name    = implode( '', $class_name );
                $control_class = 'Devmonsta\Options\Posts\Controls\\' . $class_name . '\\' . $class_name;

                if ( class_exists( $control_class ) ) {
                    $meta_owner = "post";
                    $control = new $control_class( $control_content );
                    $control->enqueue($meta_owner);
                }

            }

        }

    }

    public function get_post_files() {
        $files = [];

        foreach ( glob( get_template_directory() . '/devmonsta/options/posts/*.php' ) as $post_files ) {
            array_push( $files, $post_files );
        }

        return $files;
    }

    /** Add Metabox to the post */

    public function add_meta_box( $post_type, $args, $all_controls ) {

        add_action( 'add_meta_boxes', function () use ( $post_type, $args, $all_controls ) {

            add_meta_box(
                $args['id'],       // Unique ID / metabox ID
                $args['title'],    // Box title
                [$this, 'render'], // Content callback, must be of type callable
                $post_type,        // Post type
                'normal',
                'high',
                [$args, $all_controls]
            );
        } );

        // Adding asset files to metabox

    }

    public function render( $post_id, $arr ) {
        // print_r(json_encode($arr['args'][1]));
        $args         = $arr['args'][0];
        $all_controls = $arr['args'][1];

        if ( !empty( $all_controls ) ) {

            View::instance()->build( $args['id'], $all_controls );

        }

    }

    /** Save metbox data */

    public function save( $post_id ) {
        /**
         * ========================================
         *      Find Devmonsta metabox actions
         *       And save them into database
         * ========================================
         */

        $prefix = 'devmonsta_';

        foreach ( $_POST as $key => $value ) {

            if ( strpos( $key, $prefix ) !== false ) {
                update_post_meta(
                    $post_id,
                    $key,
                    $_POST[$key]
                );
            }

        }

    }

}
