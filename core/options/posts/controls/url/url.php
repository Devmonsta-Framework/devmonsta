<?php

namespace Devmonsta\Options\Posts\Controls\Url;

use Devmonsta\Options\Posts\Structure;

class Url extends Structure {

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

        add_action('admin_init', [$this, 'load_url_scripts']);
      }

      function load_url_scripts(){
            
        // wp_enqueue_style('devmonsta-custom-style', DM_CORE . 'options/posts/controls/url/assets/css/dm-ui.css');
         wp_enqueue_script( 'dm-url-js', DM_CORE . 'options/posts/controls/url/assets/js/script.js', [], false, true );
    
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

        $class_attributes = "class='dm-option form-field $dynamic_classes'";
        $default_attributes .= $class_attributes;
        $this->generate_markup( $default_attributes, $label, $name, $this->value, $desc );
}

    public function columns() {
        $visible = false;
        $content = $this->content;
        add_filter( 'manage_edit-' . $this->taxonomy . '_columns', function ( $columns ) use ( $content, $visible ) {

            $visible = ( isset( $content['show_in_table'] ) && $content['show_in_table'] === true ) ? true : false;

            if ( $visible ) {
                $columns[$content['name']] = __( $content['label'], 'devmonsta' );
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
        $desc  = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $value = (  ( "" != get_term_meta( $term->term_id, $name, true ) ) && ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) ) ? get_term_meta( $term->term_id, $name, true ) : "";
        
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

        $class_attributes = "class='dm-option $dynamic_classes'";
        $default_attributes .= $class_attributes;
        
        $this->generate_markup( $default_attributes, $label, $name, $value, $desc );
}

    public function generate_markup( $default_attributes, $label, $name, $value, $desc ) {
        ?>
        <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
           </div>
           <div class="dm-option-column right">
                <input class="dm-input-url"
                    type="url"
                    class="dm-option-input"
                    name="<?php echo esc_attr( $name ); ?>"
                    value="<?php echo esc_url( $value );?>" >
                <p class="dm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
    <?php
    }


}
