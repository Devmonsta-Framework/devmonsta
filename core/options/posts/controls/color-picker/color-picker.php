<?php

namespace Devmonsta\Options\Posts\Controls\ColorPicker;

use Devmonsta\Options\Posts\Structure;

class ColorPicker extends Structure {

    /**
     * @internal
     */
    public function init() {

    }

    /**
     * @internal
     */
    public function enqueue() {
        add_action( 'admin_enqueue_scripts', [$this, 'dm_enqueue_color_picker'] );
    }

    function dm_enqueue_color_picker( $hook_suffix ) {
        // first check that $hook_suffix is appropriate for your admin page
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'dm-script-handle', plugins_url( 'color-picker/assets/js/script.js', dirname( __FILE__ ) ), ['wp-color-picker'], false, true );
        
        $data                  = [];
        $data['palettes']      = isset( $this->content['palettes'] ) ? $this->content['palettes'] : false;
        wp_localize_script( 'dm-script-handle', 'color_picker_config', $data );
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
        $lable = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name  = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc  = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        ?>
        <div>
            <lable><?php echo esc_html( $lable ); ?> </lable>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <input  type="text"
                    name="<?php echo esc_html( $this->prefix . $name ); ?>"
                    value="<?php echo esc_attr( $this->value ); ?>"
                    class="dm-color-field"
                    data-default-color="<?php echo esc_attr( $this->value ); ?>" />
        </div<>
    <?php
}

}
