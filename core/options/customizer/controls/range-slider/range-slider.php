<?php
namespace Devmonsta\Options\Customizer\Controls\RangeSlider;

use Devmonsta\Options\Customizer\Structure;

class RangeSlider extends Structure {


    public $label, $name, $desc, $default_value, $properties, $value, $default_attributes;

    /**
     * @access public
     * @var    string
     */
    public $type = 'range-slider';

    public $statuses;

    public function __construct( $manager, $id, $args = [] ) {

        $this->prepare_values( $id, $args );
        $this->statuses = ['' =>esc_html__( 'Default' )];
        parent::__construct( $manager, $id, $args );
    }

    public function prepare_values( $id, $args = [] ) {
        $this->label         = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name          = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc          = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
        $this->properties    = isset( $args[0]['properties'] ) && is_array( $args[0]['properties'] ) ? $args[0]['properties'] : [];

        $from_val = isset( $args[0]['value']['from'] ) && is_numeric( $args[0]['value']['from'] ) ? $args[0]['value']['from'] : "10";
        $to_val   = isset( $args[0]['value']['to'] ) && is_numeric( $args[0]['value']['to'] ) ? $args[0]['value']['to'] : "20";
        $this->default_value = $from_val . "," . $to_val;

        //generate attributes dynamically for parent tag
        if(isset( $args[0] )){
        $this->default_attributes = $this->prepare_default_attributes( $args[0] );
        }
    }


    /**
     * @internal
     */
    public function enqueue( ) {
        // wp_enqueue_style( 'devm-range-slider-asrange-css', DEVMONSTA_CORE . 'options/posts/controls/range-slider/assets/css/asRange.css' );
        // if ( !wp_script_is( 'devm-slider-asrange', 'enqueued' ) ) {
        //     wp_enqueue_script( 'devm-slider-asrange', DEVMONSTA_CORE . 'options/posts/controls/range-slider/assets/js/jquery-asRange.min.js' );
        // }
        // wp_enqueue_script( 'devm-range-slider-from-post', DEVMONSTA_CORE . 'options/posts/controls/range-slider/assets/js/script.js', ['jquery'], time(), true );

        // wp_enqueue_script( 'devm-customizer-range-slider', DEVMONSTA_CORE . 'options/customizer/controls/range-slider/assets/js/script.js', ['jquery'], time(), true );

        // $range_slider_config       = $this->properties;
        // $range_slider_data['min']  = isset( $range_slider_config['min'] ) && is_numeric(isset( $range_slider_config['min'] )) ? $range_slider_config['min'] : 0;
        // $range_slider_data['max']  = isset( $range_slider_config['max'] ) && is_numeric(isset( $range_slider_config['max'] )) ? $range_slider_config['max'] : 100;
        // $range_slider_data['step'] = isset( $range_slider_config['step'] ) && is_numeric( $range_slider_config['step'] ) ? $range_slider_config['step'] : 1;

        // wp_localize_script( 'devm-customizer-range-slider', 'range_slider_config', $range_slider_data );

    }

    /**
     * @internal
     */
    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? $this->value() : $this->default_value;
        $this->render_content();
    }

    public function render_content() {
        $range_slider_config       = $this->properties;
        $range_slider_data['min']  = isset( $range_slider_config['min'] ) && is_numeric(isset( $range_slider_config['min'] )) ? $range_slider_config['min'] : 0;
        $range_slider_data['max']  = isset( $range_slider_config['max'] ) && is_numeric(isset( $range_slider_config['max'] )) ? $range_slider_config['max'] : 100;
        $range_slider_data['step'] = isset( $range_slider_config['step'] ) && is_numeric( $range_slider_config['step'] ) ? $range_slider_config['step'] : 1;

        ?>
        <li <?php echo devm_render_markup( $this->default_attributes ); ?>>
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="devm-option-column right">

                <input data-config='<?php echo json_encode($range_slider_config); ?>' class="devm-ctrl devm-range-slider" <?php $this->link();?>
                    type="text" value="<?php echo esc_attr( $this->value ); ?>"
                    name="<?php echo esc_attr( $this->name ); ?>" data-value="<?php echo esc_html( $this->value ); ?>"/>

                <p class="devm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>

    <?php
    }
}
