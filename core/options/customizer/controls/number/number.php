<?php
namespace Devmonsta\Options\Customizer\Controls\Number;

use Devmonsta\Options\Customizer\Structure;

class Number extends Structure {

    public $label, $name, $desc, $default_value, $value, $min, $max, $default_attributes;

    /**
     * @access public
     * @var    string
     */
    public $type = 'number';

    public $statuses;

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
        $this->default_value = isset( $args[0]['value'] ) && is_numeric( $args[0]['value'] ) ? intval( $args[0]['value'] ) : 0;
        $this->min           = isset( $args[0]['min'] ) && is_numeric( $args[0]['min'] ) ? intval( $args[0]['min'] ) : 0;
        $this->max           = isset( $args[0]['max'] ) && is_numeric( $args[0]['max'] ) ? intval( $args[0]['max'] ) : 0;

        //generate attributes dynamically for parent tag
        if(isset( $args[0] )){
        $this->default_attributes = $this->prepare_default_attributes( $args[0] );
        }
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
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? intval( $this->value() ) : $this->default_value;
        // var_dump($this->value);
        $this->render_content();
    }

    public function render_content() {
        ?>
        <li <?php echo devm_render_markup( $this->default_attributes ); ?>>
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $this->label ); ?> </label>

            </div>

            <div class="devm-option-column right">
                <input <?php $this->link();?> type="number" class="devm-option-input devm-ctrl" min="<?php echo esc_attr( $this->min ); ?>"
                    max="<?php echo esc_attr( $this->max ); ?>" name="<?php echo esc_attr( $this->name ); ?>" value="<?php echo esc_url( $this->value ); ?>" >
                <p class="devm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>

        <?php
}

}
