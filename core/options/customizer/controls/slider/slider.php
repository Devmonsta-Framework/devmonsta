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
        $this->statuses = ['' => __( 'Default' )];
        parent::__construct( $manager, $id, $args );
    }

    public function prepare_values( $id, $args = [] ) {
        $this->label         = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name          = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc          = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
        $this->default_value = isset( $args[0]['value'] ) && is_numeric( $args[0]['value'] ) ? $args[0]['value'] : 0;
        $this->properties    = isset( $args[0]['properties'] ) && is_array( $args[0]['properties'] ) ? $args[0]['properties'] : [];
    
        //generate attributes dynamically for parent tag
        $this->default_attributes = $this->prepare_default_attributes( $args[0], "active-script dm-slider-holder" );
    }

    /**
     * @internal
     */
    public function enqueue() {
        wp_enqueue_style( 'dm-slider-asrange-css', DM_CORE . 'options/posts/controls/slider/assets/css/asRange.css' );
        if ( !wp_script_is( 'dm-slider-asrange', 'enqueued' ) ) {
            wp_enqueue_script( 'dm-slider-asrange', DM_CORE . 'options/posts/controls/slider/assets/js/jquery-asRange.min.js' );
        }
        wp_enqueue_script( 'dm-slider-from-post', DM_CORE . 'options/posts/controls/slider/assets/js/script.js' );
        wp_enqueue_script( 'dm-customizer-slider-script', DM_CORE . 'options/customizer/controls/slider/assets/js/script.js', ['jquery', 'dm-slider-asrange'], time(), true );

        //get slider settings from theme
        $dm_slider_data_config  = $this->properties;
        $dm_slider_data['min']  = isset( $dm_slider_data_config['min'] ) && is_numeric( $dm_slider_data_config['min'] ) ? $dm_slider_data_config['min'] : 0;
        $dm_slider_data['max']  = isset( $dm_slider_data_config['max'] ) && is_numeric( $dm_slider_data_config['max'] ) ? $dm_slider_data_config['max'] : 100;
        $dm_slider_data['step'] = isset( $dm_slider_data_config['step'] ) && is_numeric( $dm_slider_data_config['step'] ) ? $dm_slider_data_config['step'] : 1;
        wp_localize_script( 'dm-customizer-slider-script', 'dm_slider_config', $dm_slider_data );
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
        <li <?php echo dm_render_markup( $this->default_attributes ); ?>>
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="dm-option-column right">
                <input <?php $this->link();?>  class="dm-ctrl dm-slider" type="range" name="<?php echo esc_attr( $this->name ); ?>" value="<?php echo esc_attr( $this->value ); ?>" />
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>

    <?php
    }

}
