<?php

namespace Devmonsta\Options\Posts\Controls\Radio;

use Devmonsta\Options\Posts\Structure;

class Radio extends Structure {

    /**
     * @internal
     */
    public function init() {

    }

    /**
     * @internal
     */
    public function enqueue() {

    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;
        $this->value = !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ?
        get_post_meta( $post->ID, $this->prefix . $content['name'], true )
        : $content['value'];
        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $lable   = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name    = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc    = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs   = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $choices = isset( $this->content['choices'] ) ? $this->content['choices'] : '';
        $default_attributes = "";

        if ( is_array( $attrs ) && !empty( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {
                $default_attributes .= $key . "='" . $val . "' ";
            }

        }

        ?>

        <div <?php echo esc_attr($default_attributes);?>>
            <lable><?php echo esc_html( $lable ); ?> </lable>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <?php

        if ( isset( $choices ) ) {

            foreach ( $choices as $key => $val ) {
                $is_checked = ( $key == $this->value ) ? 'checked' : '';
                ?>
                <input type="radio"
                        name="<?php echo esc_attr( $this->prefix . $name ); ?>"
                        value="<?php echo esc_attr( $key ); ?>"
                        <?php echo esc_html( $is_checked ); ?>>
                        <?php echo esc_html( $val ); ?>
                <?php
}

        }

        ?>

        </div<>
    <?php
}

}
