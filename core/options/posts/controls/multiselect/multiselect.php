<?php

namespace Devmonsta\Options\Posts\Controls\Multiselect;

use Devmonsta\Options\Posts\Structure;

class Multiselect extends Structure {

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

        add_action( 'init', [$this, 'load_multi_select_scripts'] );

    }

    /**
     * @internal
     */
    public function load_multi_select_scripts() {
        wp_enqueue_style( 'select2-css', DM_CORE . 'options/posts/controls/multiselect/assets/css/select2.min.css' );
        wp_enqueue_script( 'select2-js', DM_CORE . 'options/posts/controls/multiselect/assets/js/select2.min.js' );
        wp_enqueue_script( 'dm-multiselect-js', DM_CORE . 'options/posts/controls/multiselect/assets/js/script.js', ['jquery', 'select2-js'], time(), true );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;

        global $post;

        $default_value = ( isset( $content['value'] ) && (is_array( $content['value'] )) ) ? $content['value'] : [];
        $this->value   = (  ( $this->current_screen == "post" ) && !empty( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                            && !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
                            ? maybe_unserialize( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                            : $default_value;
        // array_unshift($this->value, "default");
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
        $choices            = isset( $this->content['choices'] ) && is_array( $this->content['choices'] ) ? $this->content['choices'] : [];
        // $choices            = ["default" => ""] + $choices;
        $default_attributes = "";
        $dynamic_classes    = "";
        // var_dump( $choices  );
        // var_dump($this->value);

        if ( is_array( $attrs ) && !empty( $attrs ) ) {
            foreach ( $attrs as $key => $val ) {
                if ( $key == "class" ) {
                    $dynamic_classes .= $val . " ";
                } else {
                    $default_attributes .= $key . "='" . $val . "' ";
                }
            }
        }

        $class_attributes = "class='dm-option form-field $dynamic_classes'";
        $default_attributes .= $class_attributes;
        $this->generate_markup( $default_attributes, $label, $name, $this->value, $desc, $choices );
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
                    $choices        = isset( $content['choices'] ) ? $content['choices'] : '';
                    $selected_value = maybe_unserialize( get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) );
                    $selected_data  = [];

                    if ( is_array( $choices ) && !empty( $choices ) ) {

                        foreach ( $choices as $key => $val ) {

                            if ( is_array( $selected_value ) && in_array( $key, $selected_value ) ) {
                                array_push( $selected_data, $val );
                            }

                        }

                        echo esc_html( join( ", ", $selected_data ) );
                    }

                }

                return $content;

            }, 10, 3 );

    }

    public function edit_fields( $term, $taxonomy ) {

        $this->load_multi_select_scripts();

        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $value              = (  ( "" != get_term_meta( $term->term_id, $name, true ) ) && ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) ) ? maybe_unserialize( get_term_meta( $term->term_id, $name, true ) ) : [];
        $choices            = isset( $this->content['choices'] ) && is_array( $this->content['choices'] )? $this->content['choices'] : [];
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

        $class_attributes = "class='dm-option term-group-wrap $dynamic_classes'";
        $default_attributes .= $class_attributes;
        $this->generate_markup( $default_attributes, $label, $name, $value, $desc, $choices );
    }

    public function generate_markup( $default_attributes, $label, $name, $value, $desc, $choices ) {
        ?>
            <div <?php echo dm_render_markup( $default_attributes ); ?> >
                <div class="dm-option-column left">
                    <label  class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
                </div>

            <div class="dm-option-column right">
                <select class="dm_multi_select" multiple="multiple" name="<?php echo esc_attr( $name ); ?>[]">
                    <?php
                        if ( is_array( $choices ) && !empty( $choices ) ) {
                            foreach ( $choices as $key => $val ) {
                                if ( is_array( $value ) && in_array( $key, $value ) ) {
                                    $selected = 'selected';
                                } else {
                                    $selected = null;
                                }

                                ?>
                                <option value="<?php echo esc_attr( $key ); ?>"
                                        <?php echo esc_html( $selected ); ?>> <?php echo esc_html( $val ); ?>
                                </option>
                                <?php
                            }
                        }
                    ?>
                </select>
                <p class="dm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
    <?php
    }

}
