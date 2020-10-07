<?php

namespace Devmonsta\Options\Posts\Controls\CheckboxMultiple;

use Devmonsta\Options\Posts\Structure;

class CheckboxMultiple extends Structure {

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

        global $post;
        $content             = $this->content;
        $default_value_array = [];

        if ( isset( $content['value'] ) && is_array( $content['value'] ) ) {
            foreach ( $content['value'] as $default_key => $default_value ) {
                if ( $default_value == true ) {
                    array_push( $default_value_array, $default_key );
                }
            }
        }

        $this->value = (  ( $this->current_screen == "post" ) && !empty( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                            && !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
                        ? maybe_unserialize( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                        : $default_value_array;

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label    = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name     = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc     = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $choices  = isset( $this->content['choices'] ) && is_array( $this->content['choices'] ) ? $this->content['choices'] : [];
        $isInline = ( $this->content['inline'] ) ? "inline" : "list";

        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $this->value, $desc, $choices, $isInline );
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
                    print_r( esc_html(  ( maybe_unserialize( $term_id, 'devmonsta_' . $column_name, true ) == true ) ) );
                }

                return $content;

            }, 10, 3 );

    }

    /**
     * @internal
     */
    public function edit_fields( $term, $taxonomy ) {
        $label   = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name    = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc    = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $value   = ( !empty( get_term_meta( $term->term_id, $name, true ) ) && !is_null( get_term_meta( $term->term_id, $name, true ) ) ) ? maybe_unserialize( get_term_meta( $term->term_id, $name, true ) ) : [];
        $choices = isset( $this->content['choices'] ) && is_array( $this->content['choices'] )? $this->content['choices'] : [];
        $isInline = ( $this->content['inline'] ) ? "inline" : "list";

        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $value, $desc, $choices, $isInline );

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
    public function generate_markup( $default_attributes, $label, $name, $value, $desc, $choices, $isInline ) {
        ?>
        <div <?php echo devm_render_markup( $default_attributes ); ?> >
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $label ); ?> </label>
            </div>

            <div class="devm-option-column right <?php echo ( $isInline ) ? esc_attr( $isInline ) : ""; ?>">
                <?php
                    if ( is_array( $choices ) && !empty( $choices ) ) {
                        foreach ( $choices as $id => $element ) {
                            if ( is_array( $value ) && in_array( $id, $value ) ) {
                                $checked = 'checked="checked"';
                            } else {
                                $checked = null;
                            }
                            ?>
                            <label class="devm-option-label-list">
                                <input class="devm-ctrl oka" type="checkbox" name="<?php echo esc_attr( $name ); ?>[]"
                                    value="<?php echo esc_attr( $id ); ?>" <?php echo esc_attr( $checked ); ?> />
                                    <?php echo esc_html( $element ); ?>
                            </label>
                            <?php
                        }
                    }
                ?>
                <input type="text" value="default" name="<?php echo esc_attr( $name ); ?>[]" style="display: none">
                <p class="devm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
        <?php
    }
}
