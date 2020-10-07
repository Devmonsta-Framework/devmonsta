<?php

namespace Devmonsta\Options\Posts\Controls\RgbaColorPicker;

use Devmonsta\Options\Posts\Structure;

class RgbaColorPicker extends Structure {

    protected $current_screen, $default_value;

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
        $this->value   = (  ( $this->current_screen == "post" )
                            && !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                            && !empty( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
                            ? get_post_meta( $post->ID, $this->prefix . $content['name'], true )
                            : ( isset( $this->content['value'] ) && preg_match('/rgba\((\s*\d+\s*,){3}[\d\.]+\)/', $this->content['value'] ) ? $this->content['value'] : "" );
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
                    echo esc_html( get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) );
                }

                return $content;
            }, 10, 3 );
    }

    /**
     * @internal
     */
    public function edit_fields( $term, $taxonomy ) {
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : "";
        $value              = (  ( "" != get_term_meta( $term->term_id, $name, true ) ) && ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) ) ? get_term_meta( $term->term_id, $name, true ) : "";
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';

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
        global $post;
        $data            = [];
        $this->default_value = isset( $this->content['value'] ) && preg_match('/rgba\((\s*\d+\s*,){3}[\d\.]+\)/', $this->content['value'] ) ? $this->content['value'] : "";
        $data['default'] = (  ( $this->current_screen == "post" )
                            && ( "" != get_post_meta( $post->ID, $this->prefix . $this->content['name'], true ) )
                            && !is_null( get_post_meta( $post->ID, $this->prefix . $this->content['name'], true ) ) )
                            ? get_post_meta( $post->ID, $this->prefix . $this->content['name'], true )
                            : $this->default_value;
        $data['palettes'] = isset( $this->content['palettes'] ) ? $this->content['palettes'] : false;
        ?>
        <div <?php echo devm_render_markup( $default_attributes ); ?> >
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $label ); ?> </label>
            </div>

            <div class="devm-option-column right">
                <input  type="text"
                        name="<?php echo esc_attr( $name ); ?>"
                        value="<?php echo esc_attr( $value ); ?>"
                        class="devm-ctrl devm-color-field color-picker-rgb"
                        data-alpha="true"
                        data-default-color="<?php echo esc_attr( $value ); ?>"
                        data-config='<?php echo json_encode($data); ?>'/>
                <p class="devm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
        <?php
    }
}
