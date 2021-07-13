<?php

namespace Devmonsta\Options\Posts\Controls\Upload;

use Devmonsta\Options\Posts\Structure;

class Upload extends Structure {

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
        add_action( 'admin_enqueue_scripts', [$this, 'load_upload_scripts'] );
    }

    /**
     * @internal
     */
    public function load_upload_scripts() {
        wp_enqueue_media();
        wp_enqueue_script( 'media-upload' );
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
        $label = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name  = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc  = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $display = '';

        $multiple   = false;
        if ( isset( $this->content['multiple'] ) && $this->content['multiple'] ) {
            $multiple = true;
        }

        // markup: image
        $str_img = $multiple ? 'Images' : 'Image';
        $image = empty($this->value) ? '<div class="devm-option-upload--item has--btn"><button type="button" class="devm-option-upload--child button">Upload '. $str_img .'</button></div>' : '' ;
        $img_ids = explode(',', $this->value);

        if ( $this->current_screen == "post" && !empty($this->value) ) {
            foreach ($img_ids as $img_id) {
                $img_url = wp_get_attachment_image_src( $img_id, 'large' );

                $image .= '<div class="devm-option-upload--item"><img src="'. $img_url[0] .'" class="devm-option-upload--child" /><button type="button" class="devm-option-upload--remove dashicons dashicons-dismiss" data-id="'. $img_id .'"></button></div>';
            }
        }

        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $this->value, $desc, $multiple, $image, $display );
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
                    $saved_value = get_term_meta( $term_id, 'devmonsta_' . $column_name, true );
                    if ( $image_attributes = wp_get_attachment_image_src( $saved_value, "full" ) ) {
                        $image = '<img src="' . $image_attributes[0] . '" class="devm-option-upload--child" />';
                        echo esc_html( $image );
                    }
                }
                return $content;
            }, 10, 3 );
    }

    /**
     * @internal
     */
    public function edit_fields( $term, $taxonomy ) {

        //load all scripts for taxonomy edit field
        $this->load_upload_scripts();

        $label      = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name       = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc       = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $value      = ( "" != get_term_meta( $term->term_id, $name, true ) ) && !is_null( get_term_meta( $term->term_id, $name, true ) ) ? get_term_meta( $term->term_id, $name, true ) : "";
        $display    = '';

        $multiple   = false;
        if ( isset( $this->content['multiple'] ) && $this->content['multiple'] ) {
            $multiple = true;
        }

        // markup: image
        $str_img = $multiple ? 'Images' : 'Image';
        $image = empty($value) ? '<div class="devm-option-upload--item has--btn"><button type="button" class="devm-option-upload--child button">Upload '. $str_img .'</button></div>' : '' ;
        $img_ids = explode(',', $value);

        if ( !empty($value) ) {
            foreach ($img_ids as $img_id) {
                $img_url = wp_get_attachment_image_src( $img_id, 'large' );

                $image .= '<div class="devm-option-upload--item"><img src="'. $img_url[0] .'" class="devm-option-upload--child" /><button type="button" class="devm-option-upload--remove dashicons dashicons-dismiss" data-id="'. $img_id .'"></button></div>';
            }
        }
        
        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $value, $desc, $multiple, $image, $display );
    }


    /**
     * Renders markup with given attributes
     *
     * @param [type] $default_attributes
     * @param [type] $label
     * @param [type] $name
     * @param [type] $value
     * @param [type] $desc
     * @param [type] $multiple
     * @param [type] $image
     * @param [type] $display
     * @return void
     */
    public function generate_markup( $default_attributes, $label, $name, $value, $desc, $multiple, $image, $display ) {
        ?>
        <div <?php echo devm_render_markup( $default_attributes ); ?> >
            <div class="devm-option-column left">
                <label  class="devm-option-label"> <?php echo esc_html( $label ); ?> </label>
            </div>
            <div class="devm-option-column right">
                <div class="devm-option-upload-wrapper">
                    <div class="devm-option-upload--list" data-multiple="<?php echo esc_attr( $multiple ); ?>">
                        <?php echo devm_render_markup( $image ); ?>
                    </div>

                    <input class='devm-ctrl devm-upload'
                           type='hidden'
                           name='<?php echo esc_attr( $name ); ?>'
                           id='<?php echo esc_attr( $name ); ?>'
                           value='<?php echo esc_attr( $value ); ?>'
                           />
                </div>
                <p class="devm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
        <?php
    }
}
