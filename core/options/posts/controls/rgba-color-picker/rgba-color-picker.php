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

        if ( $this->current_screen == "post" ) {
            $this->dm_enqueue_color_picker();
        } elseif ( $this->current_screen == "taxonomy" ) {
            add_action( 'init', [$this, 'dm_enqueue_color_picker'] );
        }

    }

    /**
     * @internal
     */
    function dm_enqueue_color_picker() {

        if ( !wp_style_is( 'wp-color-picker', 'enqueued' ) ) {
            wp_enqueue_style( 'wp-color-picker' );
        }

        // wp_enqueue_script( 'dm-rgba-handle', DM_CORE . 'options/posts/controls/rgba-color-picker/assets/js/wp-color-picker-alpha.js', ['jquery', 'wp-color-picker'], false, true );

        // global $post;
        // $data            = [];
        // $this->default_value = isset( $this->content['value'] ) && preg_match('/rgba\((\s*\d+\s*,){3}[\d\.]+\)/', $this->content['value'] ) ? $this->content['value'] : "";
        // $data['default'] = (  ( $this->current_screen == "post" )
        //                     && ( "" != get_post_meta( $post->ID, $this->prefix . $this->content['name'], true ) )
        //                     && !is_null( get_post_meta( $post->ID, $this->prefix . $this->content['name'], true ) ) )
        //                     ? get_post_meta( $post->ID, $this->prefix . $this->content['name'], true )
        //                     : $this->default_value;
        // $data['palettes'] = isset( $this->content['palettes'] ) ? $this->content['palettes'] : false;
        // wp_localize_script( 'dm-rgba-handle', 'rgba_color_picker_config', $data );
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
                    $columns[$content['name']] = __( $content['label'], 'devmonsta' );
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
        //enqueue scripts and styles for color picker
        $this->dm_enqueue_color_picker();

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
        <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
            </div>

            <div class="dm-option-column right">
                <input  type="text"
                        name="<?php echo esc_attr( $name ); ?>"
                        value="<?php echo esc_attr( $value ); ?>"
                        class="dm-ctrl dm-color-field color-picker-rgb"
                        data-alpha="true"
                        data-default-color="<?php echo esc_attr( $value ); ?>" 
                        data-config='<?php echo json_encode($data); ?>'/>
                <p class="dm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
    <?php
    }
}
