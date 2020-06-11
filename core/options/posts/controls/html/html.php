<?php

namespace Devmonsta\Options\Posts\Controls\Html;

use Devmonsta\Options\Posts\Structure;

class Html extends Structure {

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
        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $desc  = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $default_value  = isset( $this->content['value'] ) ? $this->content['value'] : '';
        
        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $default_value, $desc );

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
                    echo htmlspecialchars_decode( esc_html( $content['html'] ) );
                }

                return $content;

            }, 10, 3 );

    }

    /**
     * @internal
     */
    public function edit_fields( $term, $taxonomy ) {
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $value               = isset( $this->content['value'] ) ? $this->content['value'] : '';
                
        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $value, $desc );
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
    public function generate_markup( $default_attributes, $label, $html, $desc ) {
        ?>
            <div <?php echo dm_render_markup( $default_attributes ); ?> >
                <div class="dm-option-column left">
                    <label class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
                </div>

                <div class="dm-option-column right">
                    <div class='dm-ctrl dm_html_block'>
                        <?php echo htmlspecialchars_decode( esc_html( $html ) ); ?>
                    </div>
                    <p class="dm-option-desc"><?php echo esc_html( $desc ); ?></p>
                </div>
            </div>
    <?php
    }

}
