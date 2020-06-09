<?php

namespace Devmonsta\Options\Posts\Controls\ImagePicker;

use Devmonsta\Options\Posts\Structure;

class ImagePicker extends Structure {

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

        if ( $this->current_screen == "post" ) {
            $this->enqueue_image_picker_scripts();
        } elseif ( $this->current_screen == "taxonomy" ) {
            add_action( 'init', [$this, 'enqueue_image_picker_scripts'] );

        }

    }

    public function enqueue_image_picker_scripts() {

        // js
        wp_enqueue_script( 'dm-image-picker-js', plugins_url( 'image-picker/assets/js/image-picker.js', dirname( __FILE__ ) ), ['jquery'], time(), true );
        // css
        wp_enqueue_style( 'dm-image-picker-css', plugins_url( 'image-picker/assets/css/image-picker.css', dirname( __FILE__ ) ) );

    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;
        $default_value = isset( $content['value'] ) ? $content['value'] : "";
        $this->value   = (  ( $this->current_screen == "post" )
                        && !empty( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                        && !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
                            ? get_post_meta( $post->ID, $this->prefix . $content['name'], true )
                            : $default_value;

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $choices            = is_array( $this->content['choices'] ) && isset( $this->content['choices'] ) ? $this->content['choices'] : [];
        
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
                    $columns[$content['name']] = __( $content['label'], 'devmonsta' );
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
        //loads all scripts required for taxonomy edit field
        $this->enqueue_image_picker_scripts();

        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] )  ? $this->prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] )  ? $this->content['desc'] : '';
        $choices            = isset( $this->content['choices'] ) ? $this->content['choices'] : '';
        $value              = (  ( "" != get_term_meta( $term->term_id, $name, true ) ) && ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) ) ? get_term_meta( $term->term_id, $name, true ) : "";
        
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
            <div <?php echo dm_render_markup( $default_attributes ); ?>>
                <div class="dm-option-column left">
                    <label class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
                </div>

                <div class="dm-option-column right full-width">

                    <div class="thumbnails dm-option-image_picker_selector">
                        <input class="dm-ctrl dm-option-image-picker-input" type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
                        <ul>
                            <?php
                                if ( is_array( $choices ) && isset( $choices ) ) {

                                    foreach ( $choices as $item_key => $item ) {
                                        if(is_array($item) && isset($item)){
                                            $selected    = ( $item_key == $value ) ? 'checked' : '';
                                            $small_image = isset( $item['small'] ) ? $item['small'] : '';
                                            $large_image = isset( $item['large'] ) ? $item['large'] : '';
                                            ?>
                                            <li data-image_name='<?php echo esc_attr( $item_key ); ?>' >

                                                <label>
                                                    <input <?php echo esc_attr( $selected ); ?> id="<?php echo esc_attr( $name ) . $item_key; ?>" class="dm-ctrl dm-option-image-picker-input" type="radio" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $item_key ); ?>">

                                                    <div class="dm-img-list" for="<?php echo esc_attr( $name ) . $item_key; ?>">
                                                        <?php if ( !empty( $large_image ) ): ?>
                                                        <div class="dm-img-picker-preview">
                                                            <img src="<?php echo esc_attr( $large_image ); ?>" />
                                                        </div>
                                                        <?php endif;?>
                                                        <div class="thumbnail">
                                                            <img src="<?php echo esc_attr( $small_image ); ?>" />
                                                        </div>
                                                    </div>
                                                </label>

                                            </li>
                                            <?php
                                        }
                                    }
                                }
                            ?>
                        </ul>
                    </div>
                    <p class="dm-option-desc"><?php echo esc_html( $desc ); ?> </p>
                </div>
            </div>
    <?php
    }

}
