<?php

namespace Devmonsta\Options\Posts\Controls\ImagePicker;

use Devmonsta\Options\Posts\Structure;

class ImagePicker extends Structure {

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
        // js
        wp_enqueue_script( 'dm-image-picker-js', plugins_url( 'image-picker/assets/js/image-picker.js', dirname( __FILE__ ) ), ['jquery'], time(), true );
        // css
        wp_enqueue_style( 'dm-image-picker-css', plugins_url( 'image-picker/assets/css/image-picker.css', dirname( __FILE__ ) ) );
    }

    /**
     * @internal
     */
    public function render() {
        global $post;

        if ( !empty( get_post_meta( $post->ID, $this->prefix . 'image_picker', true ) )
            && !is_null( get_post_meta( $post->ID, $this->prefix . 'image_picker', true ) ) ) {
            $this->value = get_post_meta( $post->ID, $this->prefix . 'image_picker', true );
        }

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $help               = isset( $this->content['help'] ) ? $this->content['help'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $value              = isset( $this->content['value'] ) ? $this->content['value'] : '';
        $choices            = isset( $this->content['choices'] ) ? $this->content['choices'] : '';
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

        $class_attributes = "class='dm-option form-field $dynamic_classes'";
        $default_attributes .= $class_attributes;

        ?>
        <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
            </div>

            <div class="dm-option-column right">
                <select <?php echo esc_attr( $default_attributes ); ?> name="<?php echo esc_attr( $this->prefix . 'image_picker' ); ?>"
                value="<?php echo esc_attr( $value ); ?>" id="dm_image_picker">
                    <?php

                        foreach ( $choices as $key => $item ) {
                            $selected = $key == $this->value ? 'selected' : '';
                            echo '<option value="' . $key . '" ' . $selected . '></option>';
                        }
                    ?>
                </select>
                <ul class="thumbnails image_picker_selector">
                <?php
                    foreach ( $choices as $item_key => $item ) {
                        $selected = $item_key == $this->value ? 'selected' : '';

                        if ( is_array( $item ) ) {
                            $small_image = '';
                            $large_image = '';

                            foreach ( $item as $key => $item_size ) {

                                if ( $key == "small" ) {
                                    $small_image .= $item_size;
                                } else {
                                    $large_image .= $item_size;
                                }

                            }

                            ?>
                            <div class="tooltip">
                                <span class="tooltiptext"><img src="<?php echo esc_attr( $large_image ); ?>" height="50" width="50"/></span>
                                <li data-image_name='<?php echo esc_attr( $item_key ); ?>' class='<?php echo esc_attr( $selected ); ?>'>
                                    <div class="thumbnail">
                                        <img src="<?php echo esc_attr( $small_image ); ?>" height="50" width="50"/>
                                    </div>
                                </li>
                            </div>
                            <?php
                                    } else {
                                        ?>
                                            <li data-image_name='<?php echo esc_attr( $item_key ); ?>' class='<?php echo esc_attr( $selected ); ?>' >
                                                <div class="thumbnail">
                                                    <img src="<?php echo esc_attr( $item ); ?>" height="50" width="50" />
                                                </div>
                                            </li>
                                        <?php
                                    }

                    }

                echo '<div class="dm_help_tip">' . esc_html( $help ) . ' </div>';
                ?>
                </ul>

                <p class="dm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
    <?php
}

}
