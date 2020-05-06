<?php

namespace Devmonsta\Options\Posts\Controls\ColorPicker;

use Devmonsta\Options\Posts\Structure;

class ColorPicker extends Structure {

    protected $current_screen;

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
            $this->dm_enqueue_color_picker();
        } else

        if ( $this->current_screen == "taxonomy" ) {
            add_action( 'init', [$this, 'dm_enqueue_color_picker'] );
        }

    }

    /**
     * @internal
     */
    function dm_enqueue_color_picker() {

        if ( !wp_style_is( 'wp-color-picker', 'enqueued' ) ) {
            wp_enqueue_style( 'wp-color-picker' );
        }

        wp_enqueue_script( 'dm-script-handle', DM_CORE . 'options/posts/controls/color-picker/assets/js/script.js', ['jquery', 'wp-color-picker'], false, true );

        $data = [];

        if ( $this->current_screen == "post" ) {
            global $post;
            $data['default'] = (  ( "" != get_post_meta( $post->ID, $this->prefix . $this->content['name'], true ) )
                && !is_null( get_post_meta( $post->ID, $this->prefix . $this->content['name'], true ) ) )
            ? get_post_meta( $post->ID, $this->prefix . $this->content['name'], true )
            : $this->content['value'];
        } else {
            $data['default'] = $this->content['value'];
        }

        $data['palettes'] = isset( $this->content['palettes'] ) ? $this->content['palettes'] : false;
        wp_localize_script( 'dm-script-handle', 'color_picker_config', $data );

    }

    /**
     * @internal
     */
    public function render() {

        if ( $this->current_screen == "post" ) {
            $content = $this->content;
            global $post;
            $default_value = $content['value'];
            $this->value   = ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                && !empty( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) ?
            get_post_meta( $post->ID, $this->prefix . $content['name'], true )
            : $default_value;
        }

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $prefix             = 'devmonsta_';
        $name               = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
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
            <labelfor="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?> </labelfor=>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <input  type="text"
                    id="<?php echo $name; ?>"
                    name="<?php echo esc_attr( $name ); ?>"
                    value="<?php echo ( $this->current_screen == "post" ) ? esc_attr( $this->value ) : ""; ?>"
                    class="dm-color-field"
                    data-default-color="<?php echo ( $this->current_screen == "post" ) ? esc_attr( $this->value ) : ""; ?>" />
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
                    echo esc_html( get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) );
                }

                return $content;

            }, 10, 3 );

    }

    public function edit_fields( $term, $taxonomy ) {
        //enqueue scripts and styles for color picker
        $this->dm_enqueue_color_picker();
        $prefix             = 'devmonsta_';
        $name               = $prefix . $this->content['name'];
        $value              = get_term_meta( $term->term_id, $name, true );
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

        $class_attributes = "class='dm-option term-group-wrap $dynamic_classes'";
        $default_attributes .= $class_attributes;

        ?>

        <tr <?php echo dm_render_markup( $default_attributes ); ?> >
            <th scope="row"><label for="feature-group"><?php echo esc_html( $this->content['label'] ); ?></label></th>
            <td> <input  type="text"
                    name="<?php echo esc_attr( $name ); ?>"
                    value="<?php echo esc_attr( $value ); ?>"
                    class="dm-color-field"
                    data-default-color="<?php echo esc_attr( $value ); ?>" />
                    <br><small>(<?php echo esc_html( $desc ); ?> )</small>
            </td>
        </tr>
        <?php
}

}
