<?php

namespace Devmonsta\Options\Customizer\Controls\Gradient;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class Gradient extends \WP_Customize_Control {

    public $label, $name, $desc, $value, $choices, $default_value;

    /**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'gradient';

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
        $this->default_value = isset( $args[0]['value'] ) ? $args[0]['value'] : [];
    }

    
    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {
        
        if ( !wp_style_is( 'wp-color-picker', 'enqueued' ) ) {
            wp_enqueue_style( 'wp-color-picker' );
        }

        if ( !wp_script_is( 'dm-customizer-gradient-handle', 'enqueued' ) ) {
            wp_enqueue_script( 'dm-customizer-gradient-handle', DM_CORE . 'options/customizer/controls/gradient/assets/js/script.js', ['jquery', 'wp-color-picker'], false, true );

        }

        $data                = [];
        $default_value_array = [];

        if ( is_array( $this->default_value ) && !empty( $this->default_value ) ) {

            foreach ( $this->default_value as $default_key => $default_value ) {
                $default_value_array[$default_key] = $default_value;
            }

        }

        $data['defaults'] = $default_value_array;

        wp_localize_script( 'dm-customizer-gradient-handle', 'gradient_picker_config', $data );
    }


    /**
     * @internal
     */
    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? maybe_unserialize( $this->value() ) : $this->default_value;
        $this->render_content();
    }


    /**
     * Generates markup for specific control
     * @internal
     */
    public function render_content() {
        ?>
            <li class="dm-option">
                <div class="dm-option-column left">
                    <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
                </div>

                <div class="dm-option-column right gradient-parent">
                    <?php

                        if ( is_array( $this->value ) && !empty( $this->value ) ) {

                            foreach ( $this->value as $id => $value ) {

                                if ( $id == "secondary" ) {
                                    ?>
                                    <span class="delimiter"><?php esc_html_e( "To", "devmonsta" );?></span>
                                    <?php
                                }

                                ?>
                                    <input type="text" class="dm-ctrl dm-gradient-field-<?php echo esc_attr( $id ); ?>"
                                            name="<?php echo esc_html( $this->name . "[" . $id . "]" ); ?>"
                                            value="<?php echo esc_attr( $value ); ?>"
                                            data-default-color="<?php echo esc_attr( $value ); ?>" />
                                <?php
                            }

                        }

                    ?>
                    
                    <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
                </div>
            </li>
        <?php
    }

}
