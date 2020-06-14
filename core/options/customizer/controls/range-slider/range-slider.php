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
        $this->statuses = ['' => __( 'Default' )];
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
        $this->default_attributes = $this->prepare_default_attributes( $args[0] );
    }


    /**
     * @internal
     */
    public function enqueue( ) {
        wp_enqueue_style( 'dm-range-slider-asrange-css', DM_CORE . 'options/posts/controls/slider/assets/css/asRange.css' );
        if ( !wp_script_is( 'dm-slider-asrange', 'enqueued' ) ) {
            wp_enqueue_script( 'dm-slider-asrange', DM_CORE . 'options/posts/controls/slider/assets/js/jquery-asRange.min.js' );
        }
        wp_enqueue_script( 'dm-customizer-range-slider', DM_CORE . 'options/customizer/controls/range-slider/assets/js/script.js', ['jquery', 'dm-slider-asrange'], time(), true );

        $range_slider_config       = $this->properties;
        $range_slider_data['min']  = isset( $range_slider_config['min'] ) && is_numeric(isset( $range_slider_config['min'] )) ? $range_slider_config['min'] : 0;
        $range_slider_data['max']  = isset( $range_slider_config['max'] ) && is_numeric(isset( $range_slider_config['max'] )) ? $range_slider_config['max'] : 100;
        $range_slider_data['step'] = isset( $range_slider_config['step'] ) && is_numeric( $range_slider_config['step'] ) ? $range_slider_config['step'] : 1;

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
        <li <?php echo dm_render_markup( $this->default_attributes ); ?>>
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="dm-option-column right">

                <input class="dm-ctrl dm-range-slider" <?php $this->link();?>
                    type="text" value="<?php echo esc_attr( $this->value ); ?>"
                    name="<?php echo esc_attr( $this->name ); ?>" data-value="<?php echo esc_html( $this->value ); ?>"/>
                
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>

    <?php
    }
}
