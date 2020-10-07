<?php

namespace Devmonsta\Options\Posts\Controls\Gradient;

use Devmonsta\Options\Posts\Structure;

class Gradient extends Structure {

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

        $default_value_array = [];

        if ( is_array( $content['value'] ) && !empty( $content['value'] ) ) {

            foreach ( $content['value'] as $default_key => $default_value ) {
                $default_value_array[$default_key] = $default_value;
            }
        }

        $this->value = (  ( $this->current_screen == "post" )
                        && ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
                        && ( "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
                    ? maybe_unserialize( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                    : $default_value_array;

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
                    $color_values = maybe_unserialize( get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) );

                    if ( is_array( $color_values ) && !empty( $color_values ) ) {

                        foreach ( $color_values as $key => $value ) {
                            echo esc_html($key . ": " . $value) . "<br>";
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
        $value              = (  ( "" != get_term_meta( $term->term_id, $name, true ) ) && ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) ) ? maybe_unserialize( get_term_meta( $term->term_id, $name, true ) ) : [];

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
    public function generate_markup( $default_attributes, $label, $name, $value, $desc  ) {
        global $post;
        $data                = [];
        $default_value_array = [];

        if ( is_array( $this->content['value'] ) && !empty( $this->content['value'] ) ) {

            foreach ( $this->content['value'] as $default_key => $default_value ) {
                $default_value_array[$default_key] = preg_match('/^#[a-f0-9]{6}$/i', $default_value) ? $default_value : "#FFFFFF";
            }

        }

        $data['defaults'] = $default_value_array;
        ?>
        <div <?php echo devm_render_markup( $default_attributes ); ?> >
            <div class="devm-option-column left">
                <label  class="devm-option-label"><?php echo esc_html( $label ); ?> </label>
            </div>
            <div class="devm-option-column right">
                <?php
                if ( is_array( $value ) && isset( $value['primary'] )  && isset( $value['secondary'] ) ) {
                    ?>
                    <div class="devm-gradient-color-picker" data-config='<?php echo json_encode($data); ?>'>
                        <input type="text" class="devm-ctrl devm-gradient-field devm-gradient-field-primary"
                                name="<?php echo esc_html( $name . "[primary]" ); ?>"
                                value="<?php echo esc_attr( $value['primary'] ); ?>"
                                data-default-color="<?php echo esc_attr( $value['primary'] ); ?>" />

                        <span class="delimiter"><?php esc_html_e( "To", "devmonsta" );?></span>

                        <input type="text" class="devm-ctrl devm-gradient-field devm-gradient-field-secondary"
                                name="<?php echo esc_html( $name . "[secondary]" ); ?>"
                                value="<?php echo esc_attr( $value['secondary'] ); ?>"
                                data-default-color="<?php echo esc_attr( $value['secondary'] ); ?>" />
                    </div>
                    <?php
                }

                ?>
                <p class="devm-option-desc"><?php echo esc_html( $desc ); ?></p> </small>
            </div>
        </div>
        <?php
    }

}
