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

        add_action( 'init', [$this, 'dm_enqueue_gradient_picker'] );

    }

    /**
     * @internal
     */
    public function dm_enqueue_gradient_picker() {

        if ( !wp_style_is( 'wp-color-picker', 'enqueued' ) ) {
            wp_enqueue_style( 'wp-color-picker' );
        }

        if ( !wp_script_is( 'dm-gradient-handle', 'enqueued' ) ) {
            wp_enqueue_script( 'dm-gradient-handle', DM_CORE . 'options/posts/controls/gradient/assets/js/script.js', ['jquery', 'wp-color-picker'], false, true );

        }

        global $post;
        $data                = [];
        $default_value_array = [];

        if ( is_array( $this->content['value'] ) && !empty( $this->content['value'] ) ) {

            foreach ( $this->content['value'] as $default_key => $default_value ) {
                $default_value_array[$default_key] = $default_value;
            }

        }

        $data['defaults'] = $default_value_array;

        wp_localize_script( 'dm-gradient-handle', 'gradient_picker_config', $data );
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
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $default_attributes = "";
        $dynamic_classes    = "";

        if ( is_array( $attrs ) && !empty( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {

                if ( $key == "class" ) {
                    $dynamic_classes .= $val . " ";
                } else {
                    $default_attributes .= $key . "='" . $val . "' ";
                }

            }

        }

        
        $condition_class    = "";
        $condition_data     = "";
        if( isset( $this->content['active_callback'] ) && is_array( $this->content['active_callback'] ) ){
            $condition_class = "dm-condition-active";
            $condition_data = json_encode($this->content['active_callback'], true);
        }
        $class_attributes = "class='dm-option form-field $condition_class $dynamic_classes'";
        $default_attributes .= $class_attributes;
        $this->generate_markup( $default_attributes, $label, $name, $this->value, $desc, $condition_data );
}

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

    public function edit_fields( $term, $taxonomy ) {
        $this->dm_enqueue_gradient_picker();

        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $value              = (  ( "" != get_term_meta( $term->term_id, $name, true ) ) && ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) ) ? maybe_unserialize( get_term_meta( $term->term_id, $name, true ) ) : [];
        $default_attributes = "";
        $dynamic_classes    = "";

        if ( is_array( $attrs ) && !empty( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {

                if ( $key == "class" ) {
                    $dynamic_classes .= $val . " ";
                } else {
                    $default_attributes .= $key . "='" . $val . "' ";
                }

            }

        }

        $condition_class    = "";
        $condition_data     = "";
        if( isset( $this->content['active_callback'] ) && is_array( $this->content['active_callback'] ) ){
            $condition_class = "dm-condition-active";
            $condition_data = json_encode($this->content['active_callback'], true);
        }
        $class_attributes = "class='dm-option form-field $condition_class $dynamic_classes'";
        $default_attributes .= $class_attributes;
        
        $this->generate_markup( $default_attributes, $label, $name, $value, $desc, $condition_data );
    }

    public function generate_markup( $default_attributes, $label, $name, $value, $desc, $condition_data  ) {
        ?>
            <div <?php echo dm_render_markup( $default_attributes ); ?> >
                <div class="dm-option-column left">
                    <label  class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
                </div>
                <div class="dm-option-column right">
                    <?php

                        if ( is_array( $value ) && !empty( $value ) ) {

                            foreach ( $value as $id => $value ) {

                                if ( $id == "secondary" ) {
                                    ?>
                                    <span class="delimiter"><?php esc_html_e( "To", "devmonsta" );?></span>
                                    <?php
                                }

                                ?>
                                    <input type="text" class="dm-ctrl dm-gradient-field-<?php echo esc_attr( $id ); ?>"
                                            name="<?php echo esc_html( $name . "[" . $id . "]" ); ?>"
                                            value="<?php echo esc_attr( $value ); ?>"
                                            data-default-color="<?php echo esc_attr( $value ); ?>"
                                            <?php 
                                                if($condition_data != ""){
                                                ?>
                                                data-dm_conditions="<?php echo esc_attr( $condition_data ); ?>"
                                            <?php 
                                                }
                                            ?>/>
                                <?php
                            }

                        }

            ?>
                    <p class="dm-option-desc"><?php echo esc_html( $desc ); ?> </small>
                </div>
            </div>
    <?php
    }

}
