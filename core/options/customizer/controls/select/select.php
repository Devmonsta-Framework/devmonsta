<?php

namespace Devmonsta\Options\Customizer\Controls\Select;

use Devmonsta\Options\Customizer\Structure;

class Select extends Structure {

    public $label, $name, $desc, $value, $choices, $default_value, $default_attributes;

    /**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'select';

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
        $this->default_value = isset( $args[0]['value'] ) ? $args[0]['value'] : "";
        $this->choices       = isset( $args[0]['choices'] ) && is_array( $args[0]['choices'] ) ? $args[0]['choices'] : [];

        //generate attributes dynamically for parent tag
        if(isset( $args[0] )){
        $this->default_attributes = $this->prepare_default_attributes( $args[0] );
        }
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {
        // wp_enqueue_style( 'select2-css', DEVMONSTA_CORE . 'options/posts/controls/select/assets/css/select2.min.css' );
        // wp_enqueue_script( 'select2-js', DEVMONSTA_CORE . 'options/posts/controls/select/assets/js/select2.min.js', ['jquery'] );
        // wp_enqueue_script( 'devm-select-js', DEVMONSTA_CORE . 'options/posts/controls/select/assets/js/script.js', ['jquery', 'select2-js'], time(), true );
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
                    <select <?php $this->link(); ?> class="devm-option-input devm-ctrl devm_select" name="<?php echo esc_attr( $this->name ); ?>">
                        <?php
                            if ( is_array( $this->choices ) && !empty( $this->choices ) ) {
                                foreach ( $this->choices as $key => $val ) {
                                    $is_selected = ( $key == $this->value ) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo esc_html( $key ); ?>"
                                        <?php echo esc_html( $is_selected ); ?>>
                                        <?php echo esc_html( $val ); ?>
                                    <?php
                                }
                            }
                        ?>
                    </select>

                    <p class="devm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
                </div>
            </div>
    <?php
    }


}
