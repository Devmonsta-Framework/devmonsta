<?php
namespace Devmonsta\Options\Customizer\Controls\Switcher;

use Devmonsta\Options\Customizer\Structure;

class Switcher extends Structure {

    public $label, $name, $desc, $default_value, $value, $choices, $isInline, $default_attributes;

    /**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'switcher';

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
        $this->left_choice   = isset( $args[0]['left-choice'] ) && is_array( $args[0]['left-choice'] ) ? $args[0]['left-choice'] : [];
        $this->right_choice  = isset( $args[0]['right-choice'] ) && is_array( $args[0]['right-choice'] ) ? $args[0]['right-choice'] : [];
        $this->left_key      = $this->array_key_first( $this->left_choice );
        $this->right_key     = $this->array_key_first( $this->right_choice );

        $this->default_value = [];
        if ( isset( $args[0]['value'] ) ) {
            array_push( $this->default_value, $args[0]['value'] );
        }
        $this->choices       = [$this->right_key => $this->right_choice[$this->right_key]];

        //generate attributes dynamically for parent tag
        $this->default_attributes = $this->prepare_default_attributes( $args[0] );
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {
        wp_enqueue_style( 'devm-switcher', DEVMONSTA_CORE . 'options/posts/controls/switcher/assets/css/devm-switcher.css');
        wp_enqueue_script( 'devm-customizer-switcher', DEVMONSTA_CORE . 'options/customizer/controls/switcher/assets/js/script.js', ['jquery'], time(), true );
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

                <div class="devm-option-column right devm-switcher">
                    
                    <?php $saved_value = !is_array( $this->value ) ? explode( ',', $this->value ) : $this->value; 
        ?>

                    <ul class="devm-switcher devm_switcher_item">
                            <li >
                                <label>
                                    <input class="devm-ctrl devm-control-switcher" type="checkbox" value="<?php echo esc_attr( $this->right_key ); ?>" <?php checked( in_array( $this->right_key, $saved_value ) ); ?> />
                                    
                                    <div data-left="<?php echo esc_attr( $this->left_choice[$this->left_key] ); ?>" data-right="<?php echo esc_attr( $this->right_choice[$this->right_key] ); ?>" class='devm_switcher_label devm-option-label'></div>
                                </label>
                            </li>
                        <input data-unchecked_value="<?php echo esc_attr($this->left_key); ?>" type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( implode( ',', $saved_value ) ); ?>" />
                    </ul>
                    <p class="devm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
                </div>
            </li>
    <?php
    }

    public function array_key_first( array $array ) {

        foreach ( $array as $key => $value ) {return $key;}

    }
}
