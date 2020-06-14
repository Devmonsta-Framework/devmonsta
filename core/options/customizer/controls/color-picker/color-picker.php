<?php

namespace Devmonsta\Options\Customizer\Controls\ColorPicker;

use Devmonsta\Options\Customizer\Structure;

class ColorPicker extends Structure {

    public $label, $name, $desc, $value, $default_value, $default_attributes, $palettes;

    /**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'color-picker';

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
        $this->statuses = ['' => __( 'Default' )];
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
        $this->default_value = isset( $args[0]['value'] ) && preg_match('/^#[a-f0-9]{6}$/i', $args[0]['value']) ? $args[0]['value'] : '#FF0000';
        $this->palettes      = isset( $args[0]['palettes'] ) && is_array( $args[0]['palettes'] )? $args[0]['palettes'] : [];

        //generate attributes dynamically for parent tag
        $this->default_attributes = $this->prepare_default_attributes( $args[0], "" );
    }

    
    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {
        
        if ( !wp_style_is( 'wp-color-picker', 'enqueued' ) ) {
            wp_enqueue_style( 'wp-color-picker' );
        }
        
        wp_enqueue_script( 'dm-script-handle-from-post', DM_CORE . 'options/posts/controls/color-picker/assets/js/script.js', ['jquery', 'wp-color-picker'], false, true );

        if ( !wp_script_is( 'dm-customizer-color-handle', 'enqueued' ) ) {
            wp_enqueue_script( 'dm-customizer-color-handle', DM_CORE . 'options/customizer/controls/color-picker/assets/js/script.js', ['jquery', 'wp-color-picker', 'dm-script-handle-from-post'], false, true );
        }

        $data                   = [];
        $data['default']        = $this->default_value;
        $data['palettes']       = $this->palettes;

        wp_localize_script( 'dm-customizer-color-handle', 'dm_color_picker_config', $data );
    }


    /**
     * @internal
     */
    public function render() {
        $this->value = ( !empty($this->value()) ) ? $this->value() : $this->default_value;
        $this->render_content();
    }


    /**
     * Generates markup for specific control
     * @internal
     */
    public function render_content() {
        ?>
            <li <?php echo dm_render_markup( $this->default_attributes ); ?>>
                <div class="dm-option-column left">
                    <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
                </div>
                <div class="dm-option-column right">
                    <input <?php $this->link(); ?> type="text" class="dm-ctrl dm-color-picker-field"
                    data-value="<?php echo esc_html( $this->value ); ?>"
                            value="<?php echo esc_attr( $this->value ); ?>" data-default-color="<?php echo esc_attr( $this->value ); ?>" />            
                    <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
                </div>
            </li>
        <?php
    }

}
