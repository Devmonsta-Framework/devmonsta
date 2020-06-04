<?php
namespace Devmonsta\Options\Customizer\Controls\RangeSlider;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class RangeSlider extends \WP_Customize_Control {
    
    
    public $label, $name, $desc, $default_value, $properties, $value;

    /**
     * @access public
     * @var    string
     */
    public $type = 'range-slider';

    public $statuses;

    public function __construct( $manager, $id, $args = [] ) {

        $this->prepare_values( $id, $args );
        $this->statuses = ['' => __( 'Default' )];
        parent::__construct( $manager, $id, $args );
    }

    public function prepare_values( $id, $args = [] ) {
        $this->label         = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name          = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc          = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
        $this->properties    = isset( $args[0]['properties'] ) ? $args[0]['properties'] : [];
    
        $from_val = isset( $args[0]['value']['from'] ) ? $args[0]['value']['from'] : "10";
        $to_val   = isset( $args[0]['value']['to'] ) ? $args[0]['value']['to'] : "20";
        $this->default_value = $from_val . "," . $to_val;
    }


    /**
     * @internal
     */
    public function enqueue( ) {
        wp_enqueue_style( 'asRange-css', DM_CORE . 'options/post/controls/range-slider/assets/css/asRange.css' );
        wp_enqueue_script( 'asRange-js', DM_CORE . 'options/post/controls/range-slider/assets/js/jquery-asRange.js' );
        wp_enqueue_script( 'dm-customizer-range-slider', DM_CORE . 'options/customizer/controls/range-slider/assets/js/script.js', ['jquery', 'asRange-js'], time(), true );

        $range_slider_config       = $this->properties;
        $range_slider_data['min']  = isset( $range_slider_config['min'] ) ? $range_slider_config['min'] : 0;
        $range_slider_data['max']  = isset( $range_slider_config['max'] ) ? $range_slider_config['max'] : 100;
        $range_slider_data['step'] = isset( $range_slider_config['step'] ) ? $range_slider_config['step'] : 1;

        wp_localize_script( 'dm-customizer-range-slider', 'range_slider_config', $range_slider_data );

    }

    /**
     * @internal
     */
    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? $this->value() : $this->default_value;
        $this->render_content();
    }

    public function render_content() {
        ?>
        <li  class="dm-option">
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="dm-option-column right">
                <input class="dm-ctrl dm-range-slider" <?php $this->link();?>
                    type="text" value="<?php echo esc_attr( $this->value ); ?>"
                    name="<?php echo esc_attr( $this->name ); ?>"/>
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>

    <?php
    }
}