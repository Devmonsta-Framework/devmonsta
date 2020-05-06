<?php

namespace Devmonsta\Options\Posts;

use Devmonsta\Traits\Singleton;

class View {

    use Singleton;

    protected $meta_owner = "post";

    /**
     * Build the metbox for the post
     *
     * @access      public
     * @return      void
     */

    public function build( $box_id, $controls ) {

        foreach ( $controls as $control ) {

            if ( Validator::instance()->check( $control ) ) {

                if ( $control['box_id'] == $box_id ) {
                    $this->render( $control );
                }

            }

        }

    }

    /**
     * Render markup view for the control
     * defined in teme. It will pass the data according to the
     * control type
     *
     * @access      public
     * @return      void
     */
    public function render( $control_content ) {

        if ( isset( $control_content['type'] ) ) {
            $class_name    = explode( '-', $control_content['type'] );
            $class_name    = array_map( 'ucfirst', $class_name );
            $class_name    = implode( '', $class_name );
            $control_class = 'Devmonsta\Options\Posts\Controls\\' . $class_name . '\\' . $class_name;

            if ( class_exists( $control_class ) ) {

                $control = new $control_class( $control_content );
                $control->init();
                $control->enqueue( $this->meta_owner );
                $control->render();

            } else {
                $file = plugin_dir_path( __FILE__ ) . 'controls/' . $control_content['type'] . '/' . $control_content['type'] . '.php';

                if ( file_exists( $file ) ) {
                    include_once $file;

                    if ( class_exists( $control_class ) ) {

                        $control = new $control_class( $control_content );
                        $control->init();
                        $control->enqueue( $this->meta_owner );
                        $control->render();
                    }

                }

            }

        }

    }

}
