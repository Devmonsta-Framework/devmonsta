<?php

namespace Devmonsta\Options\Posts\Controls\Upload;

use Devmonsta\Options\Posts\Structure;

class Upload extends Structure {

    /**
     * @internal
     */
    public function init() {

    }

    /**
     * @internal
     */
    public function enqueue() {
        add_action( 'admin_enqueue_scripts', [$this, 'load_scripts'] );
    }

    /**
     * @internal
     */
    public function load_scripts( $hook ) {
         wp_enqueue_script( 'dm-upload-js', plugins_url( 'upload/assets/js/script.js', dirname( __FILE__ ) ));
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;

        if ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) {
            $this->value = get_post_meta( $post->ID, $this->prefix . $content['name'], true );
        }

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $lable = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name  = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc  = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs = isset( $this->content['attr'] ) ? $this->content['attr'] : '';

        $image_size = 'full';
        $display    = 'none';
        $multiple   = 0;
        $image      = ' button">Upload image';

        if ( isset( $this->content['multiple'] ) && $this->content['multiple'] ) {
            $multiple = true;
        }

        if ( $image_attributes = wp_get_attachment_image_src( $this->value, $image_size ) ) {

            $image   = '"><img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />';
            $display = 'inline-block';
        }
        $default_attributes = "";

        if ( is_array( $attrs ) && !empty( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {
                $default_attributes .= $key . "='" . $val . "' ";
            }

        }

        ?>

        <div <?php echo esc_attr($default_attributes);?>>
                    <div class='dm-label'> <label> <?php echo $lable; ?> </label></div>
                    <div><small><?php echo esc_html( $desc ); ?> </small></div>
                    <div class='dm-meta'>
                        <a data-multiple='<?php echo $multiple; ?>' class="dm_upload_image_button<?php echo $image;?> </a>
                        <input type='hidden' name='<?php echo $this->prefix . $name ; ?>' id='<?php echo esc_attr( $this->prefix . $name ) ;?>' value='<?php echo esc_attr($this->value) ;?>' />
                        <a href='#' class='dm_remove_image_button' style='display:inline-block;display:<?php echo $display;?>'> <?php echo esc_html__( 'Remove image', 'devmonsta' );?></a>
                    </div>
                </div>
        <?php
            }

}
