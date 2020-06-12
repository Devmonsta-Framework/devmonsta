<?php
namespace Devmonsta\Options\Customizer\Controls\Multiselect;

use Devmonsta\Options\Customizer\Structure;

class Multiselect extends Structure {

    public $label, $name, $desc, $value, $choices, $default_attributes, $default_value;

    /**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'multiselect';

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
        $this->default_value  = isset( $args[0]['value'] ) && is_array( $args[0]['value'] ) ? $args[0]['value'] : [];
        $this->choices       = isset( $args[0]['choices'] ) && is_array( $args[0]['choices'] ) ? $args[0]['choices'] : [];

        //generate attributes dynamically for parent tag
        $this->default_attributes = $this->prepare_default_attributes( $args[0], "active-script" );
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {
        wp_enqueue_style( 'select2-css', DM_CORE . 'options/posts/controls/multiselect/assets/css/select2.min.css' );
        wp_enqueue_script( 'select2-js', DM_CORE . 'options/posts/controls/multiselect/assets/js/select2.min.js' );
        wp_enqueue_script( 'dm-multiselect-js-from-post', DM_CORE . 'options/posts/controls/multiselect/assets/js/script.js', ['jquery', 'select2-js'], time(), true );
        wp_enqueue_script( 'dm-customizer-multiselect-js', DM_CORE . 'options/customizer/controls/multiselect/assets/js/script.js', ['jquery', 'select2-js', 'dm-multiselect-js-from-post'], time(), true );
    }

    /**
     * @internal
     */
    public function render() {
        $this->value = !empty( $this->value() ) && is_array( $this->value() ) ? $this->value() : $this->default_value;
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
                    <select class="dm-ctrl dm_multi_select"  <?php $this->link(); ?> multiple="multiple" style="height: 100%;">
                        <?php
                        if(isset($this->choices) && is_array($this->choices)){
                            foreach ( $this->choices as $value => $label ) {
                            ?>
                                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( in_array( $value, $this->value ) )?>> <?php echo esc_html( $label );?></option>
                            <?php
                            }
                        }
                        ?>
                    </select>
                    <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
                </div>
            </li>
    <?php
    }

}
