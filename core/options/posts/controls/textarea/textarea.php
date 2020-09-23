<?php

namespace Devmonsta\Options\Posts\Controls\Textarea;

use Devmonsta\Options\Posts\Structure;

class Textarea extends Structure {

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
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;
        $this->value   = (  ( $this->current_screen == "post" )
                            && ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
                            && ( "" != ( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) )
                        ? get_post_meta( $post->ID, $this->prefix . $content['name'], true )
                        : ( isset( $content['value'] ) ? $content['value'] : "" );

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

    public function columns() {
        $visible = false;
        $content = $this->content;
        add_filter( 'manage_edit-' . $this->taxonomy . '_columns', function ( $columns ) use ( $content, $visible ) {

            $visible = ( isset( $content['show_in_table'] ) && $content['show_in_table'] === true ) ? true : false;

            if ( $visible ) {
                $columns[$content['name']] =esc_html__( $content['label'], 'devmonsta' );
            }

            return $columns;
        } );

        $cc = $content;
        add_filter( 'manage_' . $this->taxonomy . '_custom_column', function ( $content, $column_name, $term_id ) use ( $cc ) {

            if ( $column_name == $cc['name'] ) {
                echo esc_html( get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) );
            }

            return $content;

        }, 10, 3 );

    }

    public function edit_fields( $term, $taxonomy ) {
        $label = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name  = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $value = (  ( "" != get_term_meta( $term->term_id, $name, true ) ) && ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) ) ? get_term_meta( $term->term_id, $name, true ) : "";
        $desc  = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
                
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
                <label class="devm-option-label" ><?php echo esc_html( $label ); ?> </label>
            </div>
            <div class="devm-option-column right">
                <textarea
                    rows="6"
                    class="devm-option-input devm-ctrl devm-option-textarea"
                    name="<?php echo esc_attr( $name ); ?>"><?php echo esc_attr( $value ); ?></textarea>
                <p class="devm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
        <?php
    }

}
