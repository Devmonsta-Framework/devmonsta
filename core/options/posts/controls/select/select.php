<?php

namespace Devmonsta\Options\Posts\Controls\Select;

use Devmonsta\Options\Posts\Structure;

class Select extends Structure {

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
        $this->value   = (  ( $this->current_screen == "post" ) && !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) && ( "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
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
        $choices            = isset( $this->content['choices'] ) && is_array( $this->content['choices'] ) ? $this->content['choices'] : [];

        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $this->value, $desc, $choices );
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
                    $choices        = isset( $content['choices'] ) ? $content['choices'] : '';
                    $selected_value = get_term_meta( $term_id, 'devmonsta_' . $column_name, true );
                    $selected_data  = "";

                    if ( is_array( $choices ) && !empty( $choices ) ) {
                        foreach ( $choices as $key => $val ) {
                            if ( $key == $selected_value ) {
                                $selected_data = $val;
                                break;
                            }
                        }
                        echo esc_html( $selected_data );
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
        $value              = (  ( "" != get_term_meta( $term->term_id, $name, true ) ) && ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) ) ? get_term_meta( $term->term_id, $name, true ) : "";
        $choices            = isset( $this->content['choices'] ) && is_array( $this->content['choices'] ) ? $this->content['choices'] : [];

        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $value, $desc, $choices );
    }

    /**
     * Renders markup with given attributes
     *
     * @param [type] $default_attributes
     * @param [type] $label
     * @param [type] $name
     * @param [type] $value
     * @param [type] $desc
     * @param [type] $choices
     * @return void
     */
    public function generate_markup( $default_attributes, $label, $name, $value, $desc, $choices ) {
        ?>
        <div <?php echo devm_render_markup( $default_attributes ); ?> >
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $label ); ?> </label>
            </div>

            <div class="devm-option-column right">
                <select class="devm-ctrl devm_select" name="<?php echo esc_attr( $name ); ?>">
                    <?php
                        if ( is_array( $choices ) && !empty( $choices ) ) {
                            foreach ( $choices as $key => $val ) {
                                $is_selected = ( $key == $value ) ? 'selected' : '';
                                ?>
                                <option value="<?php echo esc_html( $key ); ?>"
                                    <?php echo esc_html( $is_selected ); ?>>
                                    <?php echo esc_html( $val ); ?>
                                <?php
                            }
                        }
                    ?>
                </select>
                <span class="devm-option-desc"><?php echo esc_html( $desc ); ?> </span>
            </div>
        </div>
        <?php
    }
}
