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
        $lable = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $choices = isset( $this->content['choices'] ) ? $this->content['choices'] : '';
        ?>
        <div  <?php

                if ( is_array( $attrs ) ) {

                    foreach ( $attrs as $key => $val ) {
                        echo esc_html( $key ) . "='" . esc_attr( $val ) . "' ";
                    }

                }

                ?>>
            <lable><?php echo esc_html( $lable ); ?> </lable>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <?php

        if ( isset( $choices ) ) {

            foreach ( $choices as $key => $val ) {
                $is_checked = ( $key == $this->value ) ? 'checked' : '';
                ?>
                <input type="radio"
                        name="<?php echo esc_html( $this->prefix . $name ); ?>"
                        value="<?php echo esc_html( $key ); ?>"
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
