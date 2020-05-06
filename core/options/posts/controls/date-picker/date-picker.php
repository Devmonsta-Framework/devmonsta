<?php

namespace Devmonsta\Options\Posts\Controls\DatePicker;

use Devmonsta\Options\Posts\Structure;

class DatePicker extends Structure {

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
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $min_date           = isset( $this->content['min-date'] ) ? $this->content['min-date'] : date( 'd-m-Y' );
        $max_date           = isset( $this->content['max-date'] ) ? $this->content['max-date'] : '';
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
            <label class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
            <div><small class="dm-option-desc"><?php echo esc_html( $desc ); ?> </small></div>
            <input type="date" name="<?php echo esc_attr( $this->prefix . $name ); ?>"
                    value="<?php echo esc_attr( $this->value ); ?>"
                    min="<?php echo esc_attr( $min_date ) ?>" max="<?php echo esc_attr( $max_date ) ?>">
        </div<>
    <?php
}

}
