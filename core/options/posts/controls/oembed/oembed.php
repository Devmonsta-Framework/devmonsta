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
        $wrapper_attr['data-nonce']   = wp_create_nonce( 'action_get_oembed_response' );
        $wrapper_attr['data-preview'] = isset( $this->content['preview'] ) ? json_encode( $this->content['preview'] ) : "";

        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $this->value, $desc, $wrapper_attr );
    }


    /**
     * @internal
     */
    public function columns() {
        $visible = false;
        $content = $this->content;
        add_filter( 'manage_edit-' . $this->taxonomy . '_columns',
            function ( $columns ) use ( $content, $visible ) {

                $visible = ( isset( $content['show_in_table'] ) && $content['show_in_table'] === true ) ? true : false;

                if ( $visible ) {
                    $columns[$content['name']] =esc_html__( $content['label'], 'devmonsta' );
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


    /**
     * @internal
     */
    public function edit_fields( $term, $taxonomy ) {
        $this->enqueue_oembed_scripts();
        $label                        = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name                         = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc                         = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $value                        = (  ( "" != get_term_meta( $term->term_id, $name, true ) ) && ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) ) ? get_term_meta( $term->term_id, $name, true ) : "";
        $wrapper_attr['data-nonce']   = wp_create_nonce( 'action_get_oembed_response' );
        $wrapper_attr['data-preview'] = isset( $this->content['preview'] ) ? json_encode( $this->content['preview'] ) : "";


        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $value, $desc, $wrapper_attr );
    }

    /**
     * Renders markup with given attributes
     *
     * @param [type] $default_attributes
     * @param [type] $label
     * @param [type] $name
     * @param [type] $value
     * @param [type] $desc
     * @param [type] $wrapper_attr
     * @return void
     */
    public function generate_markup( $default_attributes, $label, $name, $value, $desc, $wrapper_attr ) {
        ?>
        <div <?php echo devm_render_markup( $default_attributes ); ?> >
        <div class="devm-option-column left">
                <label  class="devm-option-label"><?php echo esc_html( $label ); ?> </label>
            </div>
            <div class="devm-option-column right devm-oembed-input">
                <input <?php echo devm_attr_to_html( $wrapper_attr ) ?>
                        type="url" name="<?php echo esc_attr( $name ); ?>"
                        value="<?php echo esc_html( $value ); ?>"
                        class="devm-ctrl devm-oembed-url-input devm-option-input"/>
                <p class="devm-option-desc"><?php echo esc_html( $desc ); ?> </p>
                <div class="devm-oembed-preview"></div>
            </div>
        </div>
        <?php
    }

    /**
     * Fetch data from url and returns to ajax request
     *
     * @return void
     */
    public static function action_get_oembed_response() {

        // Post data array from ajax request
        $post_array = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        //Check for valid nonce
        if ( wp_verify_nonce( $post_array[ '_nonce' ], 'action_get_oembed_response' ) ) {

            $url = $post_array[ 'url'];
            $width = $post_array['preview']['width'];
            $height = $post_array['preview']['height'];
            $keep_ratio = $post_array['preview']['height'] == true;
            $iframe = empty( $keep_ratio ) ? devm_oembed_get( $url, compact( 'width', 'height' ) ) : wp_oembed_get( $url, compact( 'width', 'height' ) );

            echo devm_render_markup( $iframe );
            die();

        } else {
            echo esc_html_e('Invalid nonce', 'devmonsta');
            die();
        }
    }
}