<?php

namespace Devmonsta\Options\Posts\Controls\ImagePicker;

use Devmonsta\Options\Posts\Structure;

class ImagePicker extends Structure {

    protected $current_screen;

    protected $value;

    /**
     * @internal
     */
    public function init() {
    }

    /**
     * @internal
     */
    public function enqueue( $meta_owner ) {
        $this->current_screen = $meta_owner;

        if ( $this->current_screen == "post" ) {
            $this->enqueue_image_picker_scripts();
        } elseif ( $this->current_screen == "taxonomy" ) {
            add_action( 'init', [$this, 'enqueue_image_picker_scripts'] );

        }

    }

    public function enqueue_image_picker_scripts() {

        // js
        wp_enqueue_script( 'dm-image-picker-js', plugins_url( 'image-picker/assets/js/image-picker.js', dirname( __FILE__ ) ), ['jquery'], time(), true );
        // css
        wp_enqueue_style( 'dm-image-picker-css', plugins_url( 'image-picker/assets/css/image-picker.css', dirname( __FILE__ ) ) );

    }

    /**
     * @internal
     */
    public function render() {

        $content = $this->content;
        global $post;

        $this->value = (  ( $this->current_screen == "post" )
            && !empty( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
            && !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
        ? get_post_meta( $post->ID, $this->prefix . $content['name'], true )
        : $content['value'];

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $prefix             = 'devmonsta_';
        $name               = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
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

        $class_attributes = "class='dm-option $dynamic_classes'";
        $default_attributes .= $class_attributes;

        ?>
        <div <?php echo dm_render_markup( $default_attributes ); ?>>
            <lable class="dm-option-label"><?php echo esc_html( $label ); ?> </lable>
            <div><small class="dm-option-desc"><?php echo esc_html( $desc ); ?> </small></div>
            <select name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"
                id="dm_image_picker">
                <?php

        foreach ( $choices as $key => $item ) {
            $selected = ( $key == $this->value ) ? 'selected' : '';
            ?>
                <option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_html( $selected ); ?>></option>
                <?php
}

        ?>
            </select>
            <ul class="thumbnails image_picker_selector">
            <?php

        foreach ( $choices as $item_key => $item ) {
            $selected = ( $item_key == $this->value ) ? 'selected' : '';

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
                <span class="tooltiptext"><img src="<?php echo esc_attr( $large_image ); ?>" height="50"
                        width="50" /></span>
                <li data-image_name='<?php echo esc_attr( $item_key ); ?>' class='<?php echo esc_attr( $selected ); ?>'>
                    <div class="thumbnail">
                        <img src="<?php echo esc_attr( $small_image ); ?>" height="50" width="50" />
                    </div>
                </li>
            </div>
            <?php
} else {
                ?>
            <li data-image_name='<?php echo esc_attr( $item_key ); ?>' class='<?php echo esc_attr( $selected ); ?>'>
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
    </div>
<?php
}

    public function columns() {
        $visible = false;
        $content = $this->content;
        add_filter( 'manage_edit-' . $this->taxonomy . '_columns',
            function ( $columns ) use ( $content, $visible ) {

                $visible = ( isset( $content['show_in_table'] ) && $content['show_in_table'] === true ) ? true : false;

                if ( $visible ) {
                    $columns[$content['name']] = __( $content['label'], 'devmonsta' );
                }

                return $columns;
            } );

        $cc = $content;
        add_filter( 'manage_' . $this->taxonomy . '_custom_column',
            function ( $content, $column_name, $term_id ) use ( $cc ) {

                if ( $column_name == $cc['name'] ) {
                    echo get_term_meta( $term_id, 'devmonsta_' . $column_name, true );

                }

                return $content;

            }, 10, 3 );

    }

    public function edit_fields( $term, $taxonomy ) {
        $this->enqueue_image_picker_scripts();

        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $prefix             = 'devmonsta_';
        $name               = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
        $help               = isset( $this->content['help'] ) ? $this->content['help'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $choices            = isset( $this->content['choices'] ) ? $this->content['choices'] : '';
        $value              = get_term_meta( $term->term_id, $name, true );
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

        $class_attributes = "class='dm-option term-group-wrap $dynamic_classes'";
        $default_attributes .= $class_attributes;

        ?>

    <tr <?php echo dm_render_markup( $default_attributes ); ?> >
    <th scope="row">
        <label class="dm-option-label"><?php echo esc_html( $label ); ?></label>
    </th>
    <td>
            <select name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"
                id="dm_image_picker">
                <?php

        foreach ( $choices as $key => $item ) {
            $selected = ( $key == $value ) ? 'selected' : '';
            ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_html( $selected ); ?>></option>
                        <?php
}

        ?>
            </select>
            <ul class="thumbnails image_picker_selector">
            <?php

        foreach ( $choices as $item_key => $item ) {
            $selected = ( $item_key == $value ) ? 'selected' : '';

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
                        <span class="tooltiptext"><img src="<?php echo esc_attr( $large_image ); ?>" height="50"
                                width="50" /></span>
                        <li data-image_name='<?php echo esc_attr( $item_key ); ?>' class='<?php echo esc_attr( $selected ); ?>'>
                            <div class="thumbnail">
                                <img src="<?php echo esc_attr( $small_image ); ?>" height="50" width="50" />
                            </div>
                        </li>
                    </div>
                    <?php
} else {
                ?>
                    <li data-image_name='<?php echo esc_attr( $item_key ); ?>' class='<?php echo esc_attr( $selected ); ?>'>
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
        <br><small class="dm-option-desc">(<?php echo esc_html( $desc ); ?> )</small>
    </td>
    </tr>
<?php
}

}
