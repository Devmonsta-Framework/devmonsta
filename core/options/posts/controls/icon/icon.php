<?php

namespace Devmonsta\Options\Posts\Controls\Icon;

use Devmonsta\Options\Posts\Structure;

class Icon extends Structure {

    public $current_screen, $default_icon, $default_icon_type;
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
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;

        //grab default icon data from theme array
        $this->default_icon = isset( $content['value']['icon'] ) ? $content['value']['icon'] : "fab fa-500px";
        $this->default_icon_type = isset( $content['value']['type'] ) ? $content['value']['type'] : "devm-font-awesome";

        $icon_data              = [];
        $icon_data['icon_name'] = (  ( $this->current_screen == "post" )
                                    && ( "" != get_post_meta( $post->ID, $this->prefix . $this->content['name'], true ) )
                                    && !is_null( get_post_meta( $post->ID, $this->prefix . $this->content['name'], true ) ) )
                                    ? get_post_meta( $post->ID, $this->prefix . $this->content['name'], true )
                                    : $this->default_icon;

        $icon_data['icon_type'] = (  ( $this->current_screen == "post" ) && ( "" != get_post_meta( $post->ID, $this->prefix . $this->content['name'] . "_type", true ) )
                                    && !is_null( get_post_meta( $post->ID, $this->prefix . $this->content['name'] . "_type", true ) ) )
                                ? get_post_meta( $post->ID, $this->prefix . $this->content['name'] . "_type", true )
                                : $this->default_icon_type;

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
        $iconEncoded        = json_encode( $iconList );

        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content, "devm-vue-app" );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $desc, $iconEncoded, $this->value['icon_type'], $this->value['icon_name'] );

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
                    echo get_term_meta( $term_id, 'devmonsta_' . $column_name, true );

                }

                return $content;

            }, 10, 3 );
    }

    /**
     * @internal
     */
    public function edit_fields( $term, $taxonomy ) {

        include 'icon-data.php';
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $icon               = (  ( "" != get_term_meta( $term->term_id, $name, true ) ) && ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) ) ? get_term_meta( $term->term_id, $name, true ) : "";
        $icon_type          = (  ( "" != get_term_meta( $term->term_id, $name . "_type", true ) ) && ( !is_null( get_term_meta( $term->term_id, $name . "_type", true ) ) ) ) ? get_term_meta( $term->term_id, $name . "_type", true ) : "";

        $iconEncoded = json_encode( $iconList );

        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content, "devm-vue-app" );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $desc, $iconEncoded, $icon_type, $icon );
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
    public function generate_markup( $default_attributes, $label, $name, $desc, $iconEncoded, $icon_type, $icon_name ) {
        ?>
        <div <?php echo devm_render_markup( $default_attributes ); ?> >
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $label ); ?></label>
            </div>
            <div class="devm-option-column right devm-vue-app">
                <devm-icon-picker
                    name='<?php echo esc_attr( $name ); ?>'
                    icon_list='<?php echo devm_render_markup($iconEncoded); ?>'
                    default_icon_type='<?php echo isset( $icon_type ) ? esc_attr( $icon_type ) : ""; ?>'
                    default_icon='<?php echo isset( $icon_name ) ? esc_attr( $icon_name ) : ""; ?>'
                ></devm-icon-picker>
                <p class="devm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
        <?php
    }

}
