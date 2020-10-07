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
        $default_attributes = $this->prepare_default_attributes( $this->content, "devm-vue-app active-script" );

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

        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $value              = (  ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) && ( "" != get_term_meta( $term->term_id, $name, true ) ) ) ? maybe_unserialize( get_term_meta( $term->term_id, $name, true ) ) : [];

        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content, "devm-vue-app" );

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
                <devm-dimensions
                    :dimension="<?php echo ( isset( $value['isLinked'] ) && true == $value['isLinked'] ) ? "true" : "false"; ?>"
                    linked-name="<?php echo esc_attr( $name ); ?>[isLinked]">
                    <devm-dimensions-item
                        name="<?php echo esc_attr( $name ); ?>[top]"
                        class="devm-ctrl"
                        value="<?php echo isset( $value['top'] ) && is_numeric( $value['top'] ) ? esc_attr( intval( $value['top'] ) ) : 0; ?>"
                        label="top"
                    ></devm-dimensions-item>

                    <devm-dimensions-item
                        name="<?php echo esc_attr( $name ); ?>[right]"
                        class="devm-ctrl"
                        value="<?php echo isset( $value['right'] ) && is_numeric( $value['right'] ) ? esc_attr( intval( $value['right'] ) ) : 0; ?>"
                        label="right"
                    ></devm-dimensions-item>

                    <devm-dimensions-item
                        name="<?php echo esc_attr( $name ); ?>[bottom]"
                        class="devm-ctrl"
                        value="<?php echo isset( $value['bottom'] ) && is_numeric( $value['bottom'] ) ? esc_attr( intval( $value['bottom'] ) ) : 0; ?>"
                        label="bottom"
                    ></devm-dimensions-item>

                    <devm-dimensions-item
                        name="<?php echo esc_attr( $name ); ?>[left]"
                        class="devm-ctrl"
                        value="<?php echo isset( $value['left'] ) && is_numeric( $value['left'] )? esc_attr( intval( $value['left'] ) ) : 0; ?>"
                        label="left"
                    ></devm-dimensions-item>
                </devm-dimensions>
                <p class="devm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
        <?php
    }
}
