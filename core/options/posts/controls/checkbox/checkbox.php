<?php

namespace Devmonsta\Options\Posts\Controls\Checkbox;

use Devmonsta\Options\Posts\Structure;

class Checkbox extends Structure {

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
        $default_value = $content['value'];
        $this->value = ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                            && !empty( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) ?
                        get_post_meta( $post->ID, $this->prefix . $content['name'], true )
                        : $default_value;
        // var_dump( $this->value );
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
        $text = isset( $this->content['text'] ) ? $this->content['text'] : '';
        $is_checked = ( $this->value == 'true' ) ? 'checked' : '';
        ?>
        <div <?php

                if ( is_array( $attrs ) ) {

                    foreach ( $attrs as $key => $val ) {
                        echo esc_html( $key ) . "='" . esc_attr( $val ) . "' ";
                    }

                }

                ?>>
            <lable><?php echo esc_html( $lable ); ?> </lable>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <input type="text"
                       value="false"
                       name="<?php echo esc_html( $this->prefix . $name ); ?>"
                       style="display: none">

            <div><small><?php echo esc_html( $desc ); ?> </small></div>
                <input type="checkbox"
                        name="<?php echo esc_html( $this->prefix . $name ); ?>"
                        value="true" <?php echo esc_html( $is_checked ); ?>>
                        <?php echo esc_html($text);?>
        </div<>
    <?php
}

}
