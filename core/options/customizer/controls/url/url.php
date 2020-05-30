<?php
namespace Devmonsta\Options\Customizer\Controls\Url;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class Url extends \WP_Customize_Control {

    public $label, $name, $desc, $default_value, $value, $text;

    /**
     * @access public
     * @var    string
     */
    public $type = 'url';

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
        
        $this->label         = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name          = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc          = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
        $this->default_value = isset( $args[0]['value'] ) ? $args[0]['value'] : "";
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
        $this->value   = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? $this->value() : $this->default_value;
        // var_dump($this->value);
        $this->render_content();
    }


    
    public function render_content() {
        ?>
        <div>
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
                
            </div>

            <div class="dm-option-column right">
                <input <?php $this->link();?> class="dm-input-url" type="url" class="dm-option-input"
                    name="<?php echo esc_attr( $this->name ); ?>" value="<?php echo esc_url( $this->value );?>" >
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </div>

        <?php
    }
}
