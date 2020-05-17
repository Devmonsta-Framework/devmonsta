<?php

namespace Devmonsta\Options\Posts\Controls\Upload;

use Devmonsta\Options\Posts\Structure;

class Upload extends Structure {

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
        add_action( 'admin_enqueue_scripts', [$this, 'load_upload_scripts'] );
    }

    /**
     * @internal
     */
    public function load_upload_scripts() {
        wp_enqueue_media();
        wp_enqueue_script( 'dm-upload-js', DM_CORE . 'options/posts/controls/upload/assets/js/script.js', ['jquery', 'media-upload'] );
    }

    /**
     * @internal
     */
    public function render() {

        if ( $this->current_screen == "post" ) {
            $content = $this->content;
            global $post;

            if ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) {
                $this->value = get_post_meta( $post->ID, $this->prefix . $content['name'], true );
            }

        }

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name  = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc  = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs = isset( $this->content['attr'] ) ? $this->content['attr'] : '';

        $image_size = 'full';
        $display    = 'none';
        $multiple   = 0;
        $image      = ' button">Upload image';

        if ( isset( $this->content['multiple'] ) && $this->content['multiple'] ) {
            $multiple = true;
        }

        if ( $this->current_screen == "post" && wp_get_attachment_image_src( $this->value, $image_size ) ) {
            $image_attributes = wp_get_attachment_image_src( $this->value, $image_size );
            $image            = '"><img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />';
            $display          = 'inline-block';
        }

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
                    <label  class="dm-option-label"> <?php echo esc_html( $label ); ?> </label>
                </div>
                <div class="dm-option-column right">
                    <div>
                        <a data-multiple='<?php echo esc_attr( $multiple ); ?>' class="dm_upload_image_button<?php echo dm_render_markup( $image ); ?> </a>
                        <input type='hidden' name='<?php echo esc_attr( $name ); ?>' id='<?php echo esc_attr( $name ); ?>' value='<?php echo ( $this->current_screen == "post" ) ? esc_attr( $this->value ) : ''; ?>' />
                        <a href='#' class='dm_remove_image_button' style='display:inline-block;display:<?php echo esc_attr( $display ); ?>'> <?php echo esc_html__( 'Remove image', 'devmonsta' ); ?></a>
                    </div>
                    <p class="dm-option-desc"><?php echo esc_html( $desc ); ?> </p>
                </div>
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
                    $saved_value = get_term_meta( $term_id, 'devmonsta_' . $column_name, true );

                    if ( $image_attributes = wp_get_attachment_image_src( $saved_value, "full" ) ) {
                        $image = '<img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />';
                        echo esc_html( $image );
                    }

                }

                return $content;

            }, 10, 3 );

    }

    public function edit_fields( $term, $taxonomy ) {

        $this->load_upload_scripts();

        $label      = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name       = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc       = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs      = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $value      = get_term_meta( $term->term_id, $name, true );
        $image_size = 'full';
        $display    = 'none';
        $multiple   = 0;
        $image      = '"dm_upload_image_button button">Upload image';

        if ( isset( $this->content['multiple'] ) && $this->content['multiple'] ) {
            $multiple = true;
        }

        if ( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {
            $image   = '"dm_upload_image_button"><img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />';
            $display = 'inline-block';
        }

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
        <th scope='row'>
            <label class='dm-option-label'><?php echo esc_html( $label ); ?></label>
        </th>
        <td>
            <a data-multiple='<?php echo esc_attr( $multiple ); ?>' class=<?php echo dm_render_markup( $image ); ?> </a>
            <input type='hidden' name='<?php echo esc_attr( $name ); ?>' id='<?php echo esc_attr( $name ); ?>' value='<?php echo esc_attr( $value ); ?>' />
            <a href='#' class='dm_remove_image_button' style='display:inline-block;display:<?php echo esc_attr( $display ); ?>'> <?php echo esc_html__( 'Remove image', 'devmonsta' ); ?></a>
            <br><small class='dm-option-desc'>(<?php echo esc_html( $desc ); ?> )</small>
        </td>
    </tr>
<?php
}

}
