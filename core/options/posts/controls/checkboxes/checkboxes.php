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

        // var_dump( maybe_unserialize( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )  );
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
        $lable   = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name    = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc    = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs   = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $choices = isset( $this->content['choices'] ) ? $this->content['choices'] : '';

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
        <?php

        foreach ( $choices as $id => $element ) {

            if ( is_array( $this->value ) && in_array( $id, $this->value ) ) {
                $checked = 'checked="checked"';
            } else {
                $checked = null;
            }

            ?>
                    <input  type="checkbox"
                    name="<?php echo esc_html( $this->prefix . $name ); ?>[]"
                     value="<?php echo $id; ?>" <?php echo $checked; ?> />
                    <?php echo $element; ?>
            <?php
}

        ?>
        <input type="text" value="default" name="<?php echo esc_html( $this->prefix . $name ); ?>[]" style="display: none">

</div>
<?php

    }

}
