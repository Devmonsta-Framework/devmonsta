<?php
namespace Devmonsta\Options\Customizer\Controls\Number;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class Number extends \WP_Customize_Control {

    public $label, $name, $desc, $default_value, $value, $min, $max;

    /**
     * @access public
     * @var    string
     */
    public $type = 'number';

    public $statuses;

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
        // var_dump("asjdgasdkjashdasjkdhkasd");
        // var_dump($args[0]);
        $this->label         = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name          = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc          = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
        $this->default_value = isset( $args[0]['value'] ) && is_numeric( $args[0]['value'] ) ? intval( $args[0]['value'] ) : 0;
        $this->min           = isset( $args[0]['min'] ) && is_numeric( $args[0]['min'] ) ? intval( $args[0]['min'] ) : 0;
        $this->max           = isset( $args[0]['max'] ) && is_numeric( $args[0]['max'] ) ? intval( $args[0]['max'] ) : 0;
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
        // var_dump($this->min);
        ?>
        <div>
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>

            </div>

            <div class="dm-option-column right">
                <input <?php $this->link();?> type="number" class="dm-option-input" min="<?php echo esc_attr( $this->min ); ?>"
                    max="<?php echo esc_attr( $this->max ); ?>" name="<?php echo esc_attr( $this->name ); ?>" value="<?php echo esc_url( $this->value ); ?>" >
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </div>

        <?php
}

}