<?php

namespace Devmonsta\Options\Posts\Controls\Hidden;

use Devmonsta\Options\Posts\Structure;

class Hidden extends Structure {

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
    }

    /**
     * @internal
     */
    public function render() {

        $content = $this->content;
        global $post;
        $this->value   = ( $this->current_screen == "post" ) && ( "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true )
                        && !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) ?
                    get_post_meta( $post->ID, $this->prefix . $content['name'], true )
                    : ( isset( $content['value'] ) ? $content['value'] : "" );

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
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
        $this->generate_markup( $default_attributes, $name, $this->value );
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
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : "";
        $value              = (  ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) && ( "" != get_term_meta( $term->term_id, $name, true ) ) ) ? get_term_meta( $term->term_id, $name, true ) : "";
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

        $this->generate_markup( $default_attributes, $name, $value );
}

    public function generate_markup( $default_attributes, $name, $value ) {
        ?>
            <div <?php echo dm_render_markup( $default_attributes ); ?> >
                <div class="dm-option-column left">
                </div>
                <div class="dm-option-column right">
                    <input class="dm-ctrl" type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" >
                </div>
            </div>
    <?php
    }


}
