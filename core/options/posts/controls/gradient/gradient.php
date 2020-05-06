<?php

namespace Devmonsta\Options\Posts\Controls\Gradient;

use Devmonsta\Options\Posts\Structure;

class Gradient extends Structure {

    protected $value;

    /**
     * @internal
     */
    public function init() {

    }

    /**
     * @internal
     */
    public function enqueue() {
        // add_action( 'admin_enqueue_scripts', [$this, 'dm_enqueue_gradient_picker'] );
        add_action( 'init', [$this, 'dm_gradient'] );
    }

    public function dm_gradient() {
        $this->dm_enqueue_gradient_picker();
    }

    /**
     * @internal
     */
    public function dm_enqueue_gradient_picker() {

        if ( !wp_style_is( 'wp-color-picker', 'enqueued' ) ) {
            wp_enqueue_style( 'wp-color-picker' );
        }

        if ( !wp_script_is( 'dm-gradient-handle', 'enqueued' ) ) {
            wp_enqueue_script( 'dm-gradient-handle', DM_CORE . 'options/posts/controls/gradient/assets/js/script.js', ['jquery', 'wp-color-picker'], false, true );

        }

        global $post;
        $data                = [];
        $default_value_array = [];

        if ( is_array( $this->content['value'] ) && !empty( $this->content['value'] ) ) {

            foreach ( $this->content['value'] as $default_key => $default_value ) {
                $default_value_array[$default_key] = $default_value;
            }

        }

        $data['defaults'] = $default_value_array;

        wp_localize_script( 'dm-gradient-handle', 'gradient_picker_config', $data );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;

        $default_value_array = [];

        if ( is_array( $content['value'] ) && !empty( $content['value'] ) ) {

            foreach ( $content['value'] as $default_key => $default_value ) {
                $default_value_array[$default_key] = $default_value;
            }

        }

        $this->value = ( !empty( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
            && !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
        ? maybe_unserialize( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
        : $default_value_array;

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $default_attributes = "";
        $dynamic_classes    = "";

        if ( is_array( $attrs ) && !empty( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {

                if ( $key == "class" ) {
                    $dynamic_classes .= $val . " ";
                } else {
                    $default_attributes .= $key . "='" . $val . "' ";
                }

            }

        }

        $class_attributes = "class='dm-option $dynamic_classes'";
        $default_attributes .= $class_attributes;

        ?>
        <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <label  class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
<?php

        foreach ( $this->value as $id => $value ) {
            ?>
                            <?php echo esc_html( $id ); ?>
                            <input type="text" class="dm-gradient-field-<?php echo esc_attr( $id ); ?>"
                                name="<?php echo esc_html( $this->prefix . $name . "[" . $id . "]" ); ?>"
                                value="<?php echo esc_attr( $value ); ?>"
                                data-default-color="<?php echo esc_attr( $value ); ?>"
                                 />
<?php
}

        ?>
        </div>
    <?php
}

}
