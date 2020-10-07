<?php

namespace Devmonsta\Options\Posts\Controls\WpEditor;

use Devmonsta\Options\Posts\Structure;

class WpEditor extends Structure {

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
        
        $settings                  = [];
        $settings["wpautop"]       = ( isset( $this->content['wpautop'] ) ) ? (bool) $this->content['wpautop'] : false;
        $settings["editor_height"] = ( isset( $this->content['editor_height'] ) && is_numeric( $this->content['editor_height'] ) ) ? (int) $this->content['editor_height'] : 285;
        $settings["tinymce"]       = ( isset( $this->content['editor_type'] ) && $this->content['editor_type'] == "false" ) ? false : true;
        
        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $this->value, $desc, $settings );
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

        $label                     = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name                      = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc                      = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $value                     = (  ( "" != get_term_meta( $term->term_id, $name, true ) ) && ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) ) ? get_term_meta( $term->term_id, $name, true ) : "";
        
        $settings                  = [];
        $settings["wpautop"]       = ( isset( $this->content['wpautop'] ) ) ? (bool) $this->content['wpautop'] : false;
        $settings["editor_height"] = ( isset( $this->content['editor_height'] ) && is_numeric( $this->content['editor_height'] ) ) ? (int) $this->content['editor_height'] : 285;
        $settings["tinymce"]       = ( isset( $this->content['editor_type'] ) && $this->content['editor_type'] == "false" ) ? false : true;
        
        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $value, $desc, $settings );
    }

    /**
     * Renders markup with given attributes
     *
     * @param [type] $default_attributes
     * @param [type] $label
     * @param [type] $name
     * @param [type] $value
     * @param [type] $desc
     * @param [type] $settings
     * @return void
     */
    public function generate_markup( $default_attributes, $label, $name, $value, $desc, $settings ) {
        ob_start();
        ?>
        <div <?php echo devm_render_markup( $default_attributes ); ?> >
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $label ); ?> </label>
            </div>

            <div class="devm-option-column right">
                <?php
                    wp_editor( $value, $name, $settings );
                    $editor_html = ob_get_contents();
                    $editor_html .= "<p class='devm-option-desc'>" . esc_html( $desc ) . " </p>";
                    ob_end_clean();
                    echo devm_render_markup( $editor_html );
                ?>
            </div>
        </div>
        <?php
    }

}
