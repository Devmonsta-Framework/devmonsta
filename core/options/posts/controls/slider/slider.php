<?php

namespace Devmonsta\Options\Posts\Controls\Slider;

use Devmonsta\Options\Posts\Structure;

class Slider extends Structure {

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
        add_action( 'init', [$this, 'enqueue_slider_scripts'] );
    }

    /**
     * @internal
     */
    public function enqueue_slider_scripts() {
        wp_enqueue_style( 'devm-slider-asrange-css', DEVMONSTA_CORE . 'options/posts/controls/slider/assets/css/asRange.css' );
        if ( !wp_script_is( 'devm-slider-asrange', 'enqueued' ) ) {
            wp_enqueue_script( 'devm-slider-asrange', DEVMONSTA_CORE . 'options/posts/controls/slider/assets/js/jquery-asRange.min.js' );
        }
        wp_enqueue_script( 'devm-slider-script', DEVMONSTA_CORE . 'options/posts/controls/slider/assets/js/script.js', ['jquery', 'devm-slider-asrange'], time(), true );

        //get slider settings from theme
        $devm_slider_data_config  = isset($this->content['properties']) && is_array($this->content['properties']) ? $this->content['properties'] : [];
        $devm_slider_data['min']  = isset( $devm_slider_data_config['min'] ) && is_numeric( $devm_slider_data_config['min'] ) ? $devm_slider_data_config['min'] : 0;
        $devm_slider_data['max']  = isset( $devm_slider_data_config['max'] ) && is_numeric( $devm_slider_data_config['max'] ) ? $devm_slider_data_config['max'] : 100;
        $devm_slider_data['step'] = isset( $devm_slider_data_config['step'] ) && is_numeric( $devm_slider_data_config['step']  )? $devm_slider_data_config['step'] : 1;
        wp_localize_script( 'devm-slider-script', 'devm_slider_config', $devm_slider_data );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;

        global $post;

        $default_value = isset( $content['value'] ) && is_numeric($content['value']) ? $content['value'] : 0;
        $this->value   = (  ( $this->current_screen == "post" )
                            && ( "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                            && ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) ) ?
                        get_post_meta( $post->ID, $this->prefix . $content['name'], true )
                        : $default_value;

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        
        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $this->value, $desc );
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
                    echo esc_html( "" != get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) ? get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) : "" );
                }

                return $content;

            }, 10, 3 );
    }

    /**
     * @internal
     */
    public function edit_fields( $term, $taxonomy ) {

        //loads scripts for taxonomy edit field
        $this->enqueue_slider_scripts();

        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $value              = (  ( "" != get_term_meta( $term->term_id, $name, true ) ) && ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) ) ? get_term_meta( $term->term_id, $name, true ) : "";
        
        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $value, $desc );
    }

    /**
     * Renders markup with given attributes
     *
     * @param [type] $default_attributes
     * @param [type] $label
     * @param [type] $name
     * @param [type] $value
     * @param [type] $desc
     * @return void
     */
    public function generate_markup( $default_attributes, $label, $name, $value, $desc ) {
        ?>
        <div <?php echo devm_render_markup( $default_attributes ); ?> >
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $label ); ?> </label>
            </div>

            <div class="devm-option-column right">
                <input class="devm-ctrl devm-slider" type="range" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" />
                <p class="devm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
    <?php
    }

}
