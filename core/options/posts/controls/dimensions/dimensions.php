<?php

namespace Devmonsta\Options\Posts\Controls\Dimensions;

use Devmonsta\Options\Posts\Structure;

class Dimensions extends Structure {

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

        add_action( 'init', [$this, 'enqueue_dimensions_scripts'] );
    }

    public function enqueue_dimensions_scripts() {
        wp_enqueue_style( 'dm-dimensions-css', DM_CORE . 'options/posts/controls/dimensions/assets/css/style.css', [], time(), true );
        wp_enqueue_script( 'dm-dimensions', DM_CORE . 'options/posts/controls/dimensions/assets/js/script.js', ['jquery'], time(), true );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;
        $default_value = isset( $content['value'] ) && is_array( $content['value'] ) ? $content['value'] : [];
        $this->value   = (  ( $this->current_screen == "post" )
                        && ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
                        && ( "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
                    ? maybe_unserialize( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
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
        $default_attributes = $this->prepare_default_attributes( $this->content, "dm-vue-app active-script" );

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
                    $columns[$content['name']] = __( $content['label'], 'devmonsta' );
                }

                return $columns;
            } );

        $cc = $content;
        add_filter( 'manage_' . $this->taxonomy . '_custom_column',
            function ( $content, $column_name, $term_id ) use ( $cc ) {

                if ( $column_name == $cc['name'] ) {
                    $values = maybe_unserialize( get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) );

                    if ( is_array( $values ) && !empty( $values ) ) {

                        foreach ( $values as $key => $value ) {
                            echo esc_html($key) . ": " . esc_html($value) . "<br>";
                        }

                    }

                }

                return $content;

            }, 10, 3 );

    }

    /**
     * @internal
     */
    public function edit_fields( $term, $taxonomy ) {

        //load required scripts to run this control
        $this->enqueue_dimensions_scripts();

        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $value              = (  ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) && ( "" != get_term_meta( $term->term_id, $name, true ) ) ) ? maybe_unserialize( get_term_meta( $term->term_id, $name, true ) ) : [];
                        
        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content, "dm-vue-app" );

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
            <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
            </div>

            <div class="dm-option-column right">
                <dm-dimensions
                    :dimension="<?php echo isset( $value['isLinked'] ) ? esc_attr( $value['isLinked'] ) : 'false'; ?>" 
                    linked-name="<?php echo esc_attr( $name ); ?>[isLinked]"
                >
                    <dm-dimensions-item
                        name="<?php echo esc_attr( $name ); ?>[top]"
                        class="dm-ctrl"
                        value="<?php echo isset( $value['top'] ) && is_numeric( $value['top'] ) ? esc_attr( intval( $value['top'] ) ) : 0; ?>"
                        label="top"
                    ></dm-dimensions-item>

                    <dm-dimensions-item
                        name="<?php echo esc_attr( $name ); ?>[right]"
                        class="dm-ctrl"
                        value="<?php echo isset( $value['right'] ) && is_numeric( $value['right'] ) ? esc_attr( intval( $value['right'] ) ) : 0; ?>"
                        label="right"
                    ></dm-dimensions-item>

                    <dm-dimensions-item
                        name="<?php echo esc_attr( $name ); ?>[bottom]"
                        class="dm-ctrl"
                        value="<?php echo isset( $value['bottom'] ) && is_numeric( $value['bottom'] ) ? esc_attr( intval( $value['bottom'] ) ) : 0; ?>"
                        label="bottom"
                    ></dm-dimensions-item>

                    <dm-dimensions-item
                        name="<?php echo esc_attr( $name ); ?>[left]"
                        class="dm-ctrl"
                        value="<?php echo isset( $value['left'] ) && is_numeric( $value['left'] )? esc_attr( intval( $value['left'] ) ) : 0; ?>"
                        label="left"
                    ></dm-dimensions-item>
                </dm-dimensions>
                <p class="dm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
    <?php
    }
}
