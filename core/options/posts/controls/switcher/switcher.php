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

        add_action( 'admin_enqueue_scripts', [$this, 'load_switcher_scripts'] );
    }

    /**
     * @internal
     */
    public function load_switcher_scripts() {
        //css
        wp_enqueue_style( 'dm-switcher', plugins_url( 'switcher/assets/css/dm-switcher.css', dirname( __FILE__ ) ) );
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
        $this->generate_markup( $default_attributes, $label, $name, $this->value, $desc, $right_choice[$right_key] , $left_choice[$left_key]) ;
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
        $this->load_switcher_scripts();

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
        $this->generate_markup( $default_attributes, $label, $name, $value, $desc, $right_choice[$right_key] , $left_choice[$left_key]) ;
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
    public function generate_markup( $default_attributes, $label, $name, $value, $desc, $right_key, $left_key) {
        $is_checked = ( $value == $right_key ) ? 'checked' : '';
        ?>
        <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <div class="dm-option-column left">
                <label  class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
            </div>
            <div class="dm-option-column right dm_switcher_main_block" >
                <div class='dm_switcher_item' date-right="<?php echo esc_attr( $right_key); ?>">
                    <input type='text' class='dm-ctrl' style="display: none;" value='<?php echo esc_attr( $left_key ); ?>' name='<?php echo esc_attr( $name ); ?>' />
                    <label>
                        <input type='checkbox' class='dm-ctrl dm-control-input dm-control-switcher' value='<?php echo esc_attr( $right_key ); ?>' name='<?php echo esc_attr( $name ); ?>' <?php echo esc_attr( $is_checked ); ?>/>
                        <div data-left="<?php echo esc_attr( $left_key ); ?>" data-right="<?php echo esc_attr( $right_key ); ?>" class='dm_switcher_label dm-option-label'></div>
                    </label>
                </div>
                <p class="dm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
    <?php
    }

}
