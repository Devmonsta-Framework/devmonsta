<?php

namespace Devmonsta\Options\Posts\Controls\Oembed;

use Devmonsta\Options\Posts\Structure;

class Oembed extends Structure {


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
        add_action( 'init', [$this, 'enqueue_oembed_scripts'] );
    }

    public function enqueue_oembed_scripts() {
        wp_register_script( 'dm-oembed', DM_CORE . 'options/posts/controls/oembed/assets/js/script.js', ['underscore', 'wp-util'], time(), true );
        wp_localize_script( 'dm-oembed', 'object', ['ajaxurl' => admin_url( 'admin-ajax.php' )] );
        wp_enqueue_script( 'dm-oembed' );
        add_action( 'wp_ajax_get_oembed_response', [$this, '_action_get_oembed_response'] );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;
        $default_value = isset( $content['value'] ) ? $content['value'] : "";
        $this->value   = (  ( $this->current_screen == "post" )
                        && ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
                        && ( "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
                        ? get_post_meta( $post->ID, $this->prefix . $content['name'], true )
                        : $default_value;

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

        $wrapper_attr['data-nonce']   = wp_create_nonce( '_action_get_oembed_response' );
        $wrapper_attr['data-preview'] = json_encode( $this->content['preview'] );

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
        $this->generate_markup( $default_attributes, $label, $name, $this->value, $desc, $wrapper_attr );
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
        $this->enqueue_oembed_scripts();
        $label                        = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name                         = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc                         = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs                        = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $value                        = (  ( "" != get_term_meta( $term->term_id, $name, true ) ) && ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) ) ? get_term_meta( $term->term_id, $name, true ) : "";
        $default_attributes           = "";
        $dynamic_classes              = "";
        $wrapper_attr['data-nonce']   = wp_create_nonce( '_action_get_oembed_response' );
        $wrapper_attr['data-preview'] = json_encode( $this->content['preview'] );

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
        $this->generate_markup( $default_attributes, $label, $name, $value, $desc, $wrapper_attr );
    }

    public function generate_markup( $default_attributes, $label, $name, $value, $desc, $wrapper_attr ) {
        
        ?>
            <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <div class="dm-option-column left">
                    <label  class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
                </div>
                <div class="dm-option-column right dm-oembed-input">
                <input <?php echo dm_attr_to_html( $wrapper_attr ) ?>
                            type="url" name="<?php echo esc_attr( $name ); ?>"
                            value="<?php echo esc_html( $value ); ?>"
                            class="dm-oembed-url-input"/>
                    <p class="dm-option-desc"><?php echo esc_html( $desc ); ?> </p>
                    <div class="dm-oembed-preview"></div>
                </div>
            </div>
    <?php
    }

    public static function _action_get_oembed_response() {

        if ( wp_verify_nonce( \DM_Request::POST( '_nonce' ), '_action_get_oembed_response' ) ) {
    
            require_once DM_DIR . '/core/helpers/class-dm-request.php';
            $url = \DM_Request::POST( 'url' );
    
            $width = \DM_Request::POST( 'preview/width' );
    
            $height = \DM_Request::POST( 'preview/height' );
    
            $keep_ratio = ( \DM_Request::POST( 'preview/keep_ratio' ) === 'true' );
    
            $iframe = empty( $keep_ratio ) ?
    
            dm_oembed_get( $url, compact( 'width', 'height' ) ) :
    
            wp_oembed_get( $url, compact( 'width', 'height' ) );
    
            echo wp_kses_post($iframe);
            die();
    
        } else {
            echo esc_html_e('Invalid nonce', 'devmonsta');
            die();
        }
    }
}