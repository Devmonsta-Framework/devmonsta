<?php

namespace Devmonsta\Options\Posts\Controls\Checkboxes;

use Devmonsta\Options\Posts\Structure;

class Checkboxes extends Structure {

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
        global $post;
        $content             = $this->content;
        $default_value_array = [];

        if ( is_array( $content['value'] ) && !empty( $content['value'] ) ) {

            foreach ( $content['value'] as $default_key => $default_value ) {

                if ( $default_value == true ) {
                    array_push( $default_value_array, $default_key );
                }

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
        $label   = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name    = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc    = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs   = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $choices = isset( $this->content['choices'] ) ? $this->content['choices'] : '';

        $default_attributes = "";
        $dynamic_classes = "";
        if ( is_array( $attrs ) && !empty( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {
                if($key == "class"){
                    $dynamic_classes .= $val . " ";
                }else{
                    $default_attributes .= $key . "='" . $val . "' ";
                }
               
            }

        }
        $class_attributes = "class='dm-option $dynamic_classes'";
        $default_attributes .= $class_attributes;

        ?>
        <div <?php echo dm_render_markup($default_attributes);?> >
                <label><?php echo esc_html( $label ); ?> </label>
                <div><small><?php echo esc_html( $desc ); ?> </small></div>
        <?php

        foreach ( $choices as $id => $element ) {

            if ( is_array( $this->value ) && in_array( $id, $this->value ) ) {
                $checked = 'checked="checked"';
            } else {
                $checked = null;
            }

            ?>
                    <input  type="checkbox"
                    name="<?php echo esc_attr( $this->prefix . $name ); ?>[]"
                     value="<?php echo esc_attr($id); ?>" <?php echo esc_attr($checked); ?> />
                    <?php echo esc_html($element); ?>
            <?php
}

        ?>
        <input type="text" value="default" name="<?php echo esc_attr( $this->prefix . $name ); ?>[]" style="display: none">

</div>
<?php

    }

}
