<?php
namespace Devmonsta\Options\Customizer\Controls\CheckboxMultiple;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class CheckboxMultiple extends \WP_Customize_Control {

    public $label, $name, $desc, $default_value, $value, $choices, $isInline;

    /**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'checkbox-multiple';

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
        $this->isInline      = ( $args[0]['inline'] ) ? "inline" : "list";
        $this->default_value = isset( $args[0]['value'] ) ? $args[0]['value'] : [];
        $this->choices       = isset( $args[0]['choices'] ) && is_array( $args[0]['choices'] ) ? $args[0]['choices'] : [];
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {
        wp_enqueue_script( 'dm-checkbox-multiple', DM_CORE . 'options/customizer/controls/checkbox-multiple/assets/js/script.js', ['jquery'], time(), true );
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
            <div>
                <div class="dm-option-column left">
                    <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
                </div>

                <div class="dm-option-column right <?php echo ( $this->isInline ) ? esc_attr( $this->isInline ) : ""; ?>">
                    
                    <?php $multi_values = !is_array( $this->value() ) ? explode( ',', $this->value() ) : $this->value(); ?>

                    <ul class="customize-control">
                        <?php foreach ( $this->choices as $value => $label ) : ?>

                            <li>
                                <label>
                                    <input class="customize-control-checkbox-multiple" type="checkbox" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $multi_values ) ); ?> />
                                    <?php echo esc_html( $label ); ?>
                                </label>
                            </li>

                        <?php endforeach; ?>
                        <input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( implode( ',', $multi_values ) ); ?>" />

                    </ul>
                    <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
                </div>
            </div>
    <?php
    }

}
