<?php
namespace Devmonsta\Options\Customizer\Controls\Slider;

use Devmonsta\Options\Customizer\Structure;

class Slider extends Structure {

    public $label, $name, $desc, $default_value, $properties, $value, $default_attributes;

    /**
     * @access public
     * @var    string
     */
    public $type = 'slider';

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
        $this->default_value = isset( $args[0]['value'] ) && is_numeric( $args[0]['value'] ) ? $args[0]['value'] : 0;
        $this->properties    = isset( $args[0]['properties'] ) && is_array( $args[0]['properties'] ) ? $args[0]['properties'] : [];

        //generate attributes dynamically for parent tag
        if(isset( $args[0] )){
        $this->default_attributes = $this->prepare_default_attributes( $args[0], "devm-slider-holder" );
        }
    }

    /**
     * @internal
     */
    public function enqueue() {
        // wp_enqueue_style( 'devm-slider-asrange-css', DEVMONSTA_CORE . 'options/posts/controls/slider/assets/css/asRange.css' );
        // if ( !wp_script_is( 'devm-slider-asrange', 'enqueued' ) ) {
        //     wp_enqueue_script( 'devm-slider-asrange', DEVMONSTA_CORE . 'options/posts/controls/slider/assets/js/jquery-asRange.min.js' );
        // }
        // wp_enqueue_script( 'devm-slider-from-post', DEVMONSTA_CORE . 'options/posts/controls/slider/assets/js/script.js' );
        // wp_enqueue_script( 'devm-customizer-slider-script', DEVMONSTA_CORE . 'options/customizer/controls/slider/assets/js/script.js', ['jquery', 'devm-slider-asrange'], time(), true );

        //get slider settings from theme
        // $devm_slider_data_config  = $this->properties;
        // $devm_slider_data['min']  = isset( $devm_slider_data_config['min'] ) && is_numeric( $devm_slider_data_config['min'] ) ? $devm_slider_data_config['min'] : 0;
        // $devm_slider_data['max']  = isset( $devm_slider_data_config['max'] ) && is_numeric( $devm_slider_data_config['max'] ) ? $devm_slider_data_config['max'] : 100;
        // $devm_slider_data['step'] = isset( $devm_slider_data_config['step'] ) && is_numeric( $devm_slider_data_config['step'] ) ? $devm_slider_data_config['step'] : 1;

        // wp_localize_script( 'devm-customizer-slider-script', 'devm_slider_config', $devm_slider_data );
    }

    /**
     * @internal
     */
    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? $this->value() : $this->default_value;
        $this->render_content();
    }

    public function render_content() {
        //get slider settings from theme
        $devm_slider_data_config  = $this->properties;
        $devm_slider_data['min']  = isset( $devm_slider_data_config['min'] ) && is_numeric( $devm_slider_data_config['min'] ) ? $devm_slider_data_config['min'] : 0;
        $devm_slider_data['max']  = isset( $devm_slider_data_config['max'] ) && is_numeric( $devm_slider_data_config['max'] ) ? $devm_slider_data_config['max'] : 100;
        $devm_slider_data['step'] = isset( $devm_slider_data_config['step'] ) && is_numeric( $devm_slider_data_config['step'] ) ? $devm_slider_data_config['step'] : 1;

        ?>
        <li <?php echo devm_render_markup( $this->default_attributes ); ?>>
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="devm-option-column right">
                <input min="<?php echo esc_attr($devm_slider_data['min']); ?>" max="<?php echo esc_attr($devm_slider_data['max']); ?>" step="<?php echo esc_attr($devm_slider_data['step']); ?>"  <?php $this->link();?> data-value="<?php echo esc_html( $this->value ); ?>" class="devm-ctrl devm-slider" type="range" name="<?php echo esc_attr( $this->name ); ?>" value="<?php echo esc_attr( $this->value ); ?>" />
                <p class="devm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>

    <?php
    }

}
