<?php

namespace Devmonsta\Options\Posts\Controls\Switcher;

use Devmonsta\Options\Posts\Structure;

class Switcher extends Structure {

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
        $default_value = isset( $content['value'] ) ? $content['value'] : "";
        $this->value   = (  ( $this->current_screen == "post" )
                            && ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
                            && ( "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
                        ? get_post_meta( $post->ID, $this->prefix . $content['name'], true )
                        : $default_value;

        $this->output();
    }

    public function array_key_first( array $array ) {

        foreach ( $array as $key => $value ) {return $key;}

    }

    /**
     * @internal
     */
    public function output() {
        $label        = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name         = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc         = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $left_choice  = isset( $this->content['left-choice'] ) ? $this->content['left-choice'] : '';
        $right_choice = isset( $this->content['right-choice'] ) ? $this->content['right-choice'] : '';
        $left_key     = $this->array_key_first( $left_choice );
        $right_key    = $this->array_key_first( $right_choice );

        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $this->value, $desc, $right_key , $left_key, $right_choice[$right_key], $left_choice[$left_key]) ;
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
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $left_choice        = isset( $this->content['left-choice'] ) ? $this->content['left-choice'] : '';
        $right_choice       = isset( $this->content['right-choice'] ) ? $this->content['right-choice'] : '';
        $left_key           = $this->array_key_first( $left_choice );
        $right_key          = $this->array_key_first( $right_choice );
        $value              = (  ( "" != get_term_meta( $term->term_id, $name, true ) ) && ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) ) ? get_term_meta( $term->term_id, $name, true ) : "";

        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $value, $desc, $right_key , $left_key, $right_choice[$right_key], $left_choice[$left_key]) ;
    }

    /**
     * Renders markup with given attributes
     *
     * @param [type] $default_attributes
     * @param [type] $label
     * @param [type] $name
     * @param [type] $value
     * @param [type] $desc
     * @param [type] $right_key
     * @param [type] $left_key
     * @return void
     */
    public function generate_markup( $default_attributes, $label, $name, $value, $desc, $right_key, $left_key, $right_value, $left_value) {
        $is_checked = ( $value == $right_key ) ? 'checked' : '';
        ?>
        <div <?php echo devm_render_markup( $default_attributes ); ?> >
            <div class="devm-option-column left">
                <label  class="devm-option-label"><?php echo esc_html( $label ); ?> </label>
            </div>
            <div class="devm-option-column right devm_switcher_main_block" >
                <div class='devm_switcher_item'>
                    <input type='text' style="display: none;" value='<?php echo esc_attr( $left_key ); ?>' name='<?php echo esc_attr( $name ); ?>' />
                    <label>
                        <input type='checkbox' class='devm-ctrl devm-control-input devm-control-switcher' data-right_key="<?php echo esc_attr( $right_key ); ?>" data-left_key="<?php echo esc_attr( $left_key ); ?>" value='<?php echo esc_attr( $right_key ); ?>' name='<?php echo esc_attr( $name ); ?>' <?php echo esc_attr( $is_checked ); ?>/>
                        <div data-left="<?php echo esc_attr( $left_value ); ?>" data-right="<?php echo esc_attr( $right_value ); ?>" class='devm_switcher_label devm-option-label'></div>
                    </label>
                </div>
                <p class="devm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
        <?php
    }

}
