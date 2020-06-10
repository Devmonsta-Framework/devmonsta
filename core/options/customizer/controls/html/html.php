<?php

namespace Devmonsta\Options\Customizer\Controls\Html;

use Devmonsta\Options\Customizer\Structure;

class Html extends Structure {

    public $label, $name, $desc, $value, $default_value, $default_attributes;

    /**
     * The type of customize control being rendered.
     *
     * @since  1.0.0
     * @access public
     * @var    string
     */
    public $type = 'html';

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
        $this->default_value = isset( $args[0]['value'] ) ? $args[0]['value'] : "";

        //generate attributes dynamically for parent tag
        $this->default_attributes = $this->prepare_default_attributes( $args[0] );
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {

    }

    /**
     * @internal
     */
    public function render() {
        $this->value = $this->default_value;
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
                    <div class='dm-ctrl dm_html_block'>
                        <?php echo htmlspecialchars_decode( esc_html( $this->default_value ) ); ?>
                    </div>

                    <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
                </div>
            </li>
        <?php
    }

}
