<?php

namespace Devmonsta\Options\Posts\Controls\RgbaColorPicker;

use Devmonsta\Options\Posts\Structure;

class RgbaColorPicker extends Structure {
    /**
     * @internal
     */
    public function init() {

    }

    /**
     * @internal
     */
    public function enqueue() {
        $this->dm_enqueue_color_picker();
    }

    /**
     * @internal
     */
    function dm_enqueue_color_picker() {
        if ( !wp_style_is( 'wp-color-picker', 'enqueued' ) ) {
            wp_enqueue_style( 'wp-color-picker' );
        }

        wp_enqueue_script( 'dm-rgba-handle', DM_CORE . 'options/posts/controls/rgba-color-picker/assets/js/wp-color-picker-alpha.js', ['jquery', 'wp-color-picker'], false, true );

        global $post;
        $data            = [];
        $data['default'] = ( !is_null( get_post_meta( $post->ID, $this->prefix . $this->content['name'], true ) ) ) 
                            ? get_post_meta( $post->ID, $this->prefix . $this->content['name'], true )
                            : $this->content['value'];
        $data['palettes'] = isset( $this->content['palettes'] ) ? $this->content['palettes'] : false;
        wp_localize_script( 'dm-rgba-handle', 'rgba_color_picker_config', $data );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;
        $default_value = $content['value'];
        $this->value   = ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                            && !empty( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) ?
                                get_post_meta( $post->ID, $this->prefix . $content['name'], true )
                                : $default_value;
        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name  = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc  = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $default_attributes = "";

        if ( is_array( $attrs ) && !empty( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {
                $default_attributes .= $key . "='" . $val . "' ";
            }

        }

        ?>

        <div <?php echo esc_attr($default_attributes);?>>
            <label><?php echo esc_html( $label ); ?> </label>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <input  type="text"
                    name="<?php echo esc_attr( $this->prefix . $name ); ?>"
                    value="<?php echo esc_attr( $this->value ); ?>"
                    class="dm-color-field color-picker-rgb"
                    data-alpha="true"
                    data-default-color="<?php echo esc_attr( $this->value ); ?>" />
        </div<>
    <?php
}

}
