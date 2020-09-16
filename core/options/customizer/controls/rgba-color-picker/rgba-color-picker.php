<?php

namespace Devmonsta\Options\Customizer\Controls\RgbaColorPicker;

use Devmonsta\Options\Customizer\Structure;

class RgbaColorPicker extends Structure {

    public $label, $name, $desc, $value, $default_value, $palettes, $default_attributes;

    /**
     * The type of customize control being rendered.
     *
     * @since  1.0.0
     * @access public
     * @var    string
     */
    public $type = 'rgba-color-picker';

    public $statuses;

    /**
     * Constructor of this control. Must call parent constructor
     *
     * @since  1.0.0
     * @access public
     * @var    string
     */
    public function __construct( $manager, $id, $args = [] ) {
        $this->prepare_values( $id, $args );
        $this->statuses = ['' =>esc_html__( 'Default' )];
        parent::__construct( $manager, $id, $args );
    }

    /**
     * Prepare default values passed from theme
     *
     * @param [type] $id
     * @param array $args
     * @return void
     */
    private function prepare_values( $id, $args = [] ) {
        $this->label         = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name          = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc          = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
        $this->default_value = isset( $args[0]['value'] ) && preg_match('/rgba\((\s*\d+\s*,){3}[\d\.]+\)/', $args[0]['value']) ? $args[0]['value'] : "";
        $this->palettes      = isset( $args[0]['palettes'] ) && is_array( $args[0]['palettes'] )? $args[0]['palettes'] : [];

        //generate attributes dynamically for parent tag
        $this->default_attributes = $this->prepare_default_attributes( $args[0], "active-script" );
    }

    /**
     * @internal
     */
    public function enqueue() {

        if ( !wp_style_is( 'wp-color-picker', 'enqueued' ) ) {
            wp_enqueue_style( 'wp-color-picker' );
        }
        wp_enqueue_script( 'devm-rgba-handle-from-post', DEVMONSTA_CORE . 'options/posts/controls/rgba-color-picker/assets/js/wp-color-picker-alpha.js', ['jquery', 'wp-color-picker'], false, true );
        wp_enqueue_script( 'devm-customizer-rgba-handle', DEVMONSTA_CORE . 'options/customizer/controls/rgba-color-picker/assets/js/wp-color-picker-alpha.js', ['jquery', 'wp-color-picker', 'devm-rgba-handle-from-post'], false, true );

        $data             = [];
        $data['default']  = $this->default_value;
        $data['palettes'] = $this->palettes;
        wp_localize_script( 'devm-customizer-rgba-handle', 'rgba_color_picker_config', $data );
    }

    /**
     * @internal
     */
    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? $this->value() : $this->default_value;
        $this->render_content();
    }


    /**
     * Generates markup for specific control
     * @internal
     */
    public function render_content() {
        ?>
        <li <?php echo devm_render_markup( $this->default_attributes ); ?>>
                <div class="devm-option-column left">
                    <label class="devm-option-label"><?php echo esc_html( $this->label ); ?> </label>
                </div>

                <div class="devm-option-column right">
                    <input type="text" <?php $this->link(); ?>
                        name="<?php echo esc_attr( $this->name ); ?>"
                        value="<?php echo esc_attr( $this->value ); ?>"
                        class="devm-ctrl devm-color-field color-picker-rgb"
                        data-alpha="true"
                        data-default-color="<?php echo esc_attr( $this->value ); ?>" />
                    
                    <p class="devm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
                </div>
            </li>
        <?php
    }

}
