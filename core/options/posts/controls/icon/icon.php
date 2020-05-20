<?php

namespace Devmonsta\Options\Posts\Controls\Icon;

use Devmonsta\Options\Posts\Structure;

class Icon extends Structure {

    /**
     * @internal
     */
    public function init() {

    }

    /**
     * @internal
     */
    public function enqueue( $current_screen ) {
        $this->current_screen = $current_screen;
        add_action( 'init', [$this, 'enqueue_icon_scripts'] );
    }

    public function enqueue_icon_scripts() {
        wp_enqueue_style( 'dm-fontawesome-css', DM_CORE . 'options/posts/controls/icon/assets/css/font-awesome.min.css' );
        wp_enqueue_style( 'dm-main-css', DM_CORE . 'options/posts/controls/icon/assets/css/main.css' );
        wp_enqueue_script( 'dm-asicon', DM_CORE . 'options/posts/controls/icon/assets/js/script.js', ['jquery'], time(), true );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;

        $icon_data              = [];
        $icon_data['icon_name'] = (  ( $this->current_screen == "post" )
            && ( "" != get_post_meta( $post->ID, $this->prefix . $this->content['name'], true ) )
            && !is_null( get_post_meta( $post->ID, $this->prefix . $this->content['name'], true ) ) )
        ? get_post_meta( $post->ID, $this->prefix . $this->content['name'], true )
        : "";

        $icon_data['icon_type'] = (  ( $this->current_screen == "post" ) && ( "" != get_post_meta( $post->ID, $this->prefix . $this->content['name'] . "_type", true ) )
            && !is_null( get_post_meta( $post->ID, $this->prefix . $this->content['name'] . "_type", true ) ) )
        ? get_post_meta( $post->ID, $this->prefix . $this->content['name'] . "_type", true )
        : "dm-font-awesome";

        $this->value = $icon_data;
        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        include 'icon-data.php';
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
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

        $class_attributes = "class='dm-vue-app dm-option form-field $dynamic_classes'";
        $default_attributes .= $class_attributes;
        $iconEncoded = json_encode( $iconList );
        $this->generate_markup( $default_attributes, $label, $name, $desc, $iconEncoded, $this->value['icon_type'], $this->value['icon_name'] );

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
        $this->enqueue_icon_scripts();

        include 'icon-data.php';
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $icon               = (  ( "" != get_term_meta( $term->term_id, $name, true ) ) && ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) ) ? get_term_meta( $term->term_id, $name, true ) : "";
        $icon_type          = (  ( "" != get_term_meta( $term->term_id, $name . "_type", true ) ) && ( !is_null( get_term_meta( $term->term_id, $name . "_type", true ) ) ) ) ? get_term_meta( $term->term_id, $name . "_type", true ) : "";
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

        $class_attributes = "class='dm-vue-app dm-option form-field dm-box $dynamic_classes'";
        $default_attributes .= $class_attributes;
        $iconEncoded = json_encode( $iconList );
        $this->generate_markup( $default_attributes, $label, $name, $desc, $iconEncoded, $icon_type, $icon );

}

    public function generate_markup( $default_attributes, $label, $name, $desc, $iconEncoded, $icon_type, $icon_name ) {
        ?>
        <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $label ); ?></label>
            </div>
            <div class="dm-option-column right">
                <dm-icon-picker
                    name='<?php echo esc_attr( $name ); ?>'
                    icon_list='<?php echo dm_render_markup($iconEncoded); ?>'
                    default_icon_type='<?php echo isset( $icon_type ) ? esc_attr( $icon_type ) : "dm-font-awesome"; ?>'
                    default_icon='<?php echo isset( $icon_name ) ? esc_attr( $icon_name ) : "fas fa-angle-right"; ?>'
                ></dm-icon-picker>
                <p class="dm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
        <?php
}

}
