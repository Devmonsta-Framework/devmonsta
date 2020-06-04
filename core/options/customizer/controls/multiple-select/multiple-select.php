<?php
namespace Devmonsta\Options\Customizer\Controls\MultipleSelect;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class MultipleSelect extends \WP_Customize_Control {

    public $label, $name, $desc, $value, $choices;

    /**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'multiple-select';

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
        $this->choices       = isset( $args[0]['choices'] ) && is_array( $args[0]['choices'] ) ? $args[0]['choices'] : [];
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {
        wp_enqueue_style( 'select2-css', DM_CORE . 'options/customizer/controls/multiple-select/assets/css/select2.min.css' );
        wp_enqueue_script( 'select2-js', DM_CORE . 'options/customizer/controls/multiple-select/assets/js/select2.min.js' );
        wp_enqueue_script( 'dm-customizer-multiselect-js', DM_CORE . 'options/customizer/controls/multiple-select/assets/js/script.js', ['jquery', 'select2-js'], time(), true );
    
    }

    /**
     * @internal
     */
    public function render() {
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

                <div class="dm-option-column right">
                    <select class="dm-ctrl dm_multi_select"  <?php $this->link(); ?> multiple="multiple" style="height: 100%;">
                        <?php
                        foreach ( $this->choices as $value => $label ) {
                            $selected = ( in_array( $value, $this->value() ) ) ? selected( 1, 1, false ) : '';
                            echo '<option value="' . esc_attr( $value ) . '"' . $selected . '>' . $label . '</option>';
                        }
                        ?>
                    </select>
                    <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
                </div>
            </li>
    <?php
    }

}