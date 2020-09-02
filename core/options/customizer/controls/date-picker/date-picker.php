<?php
namespace Devmonsta\Options\Customizer\Controls\DatePicker;

use Devmonsta\Options\Customizer\Structure;

class DatePicker extends Structure {

    public $label, $name, $desc, $value, $monday_first, $min_date, $max_date,
            $default_value, $default_attributes;

    /**
     * @access public
     * @var    string
     */
    public $type = 'date-picker';

    public $statuses;

    public function __construct( $manager, $id, $args = [] ) {

        $this->prepare_values( $id, $args );
        $this->statuses = ['' =>esc_html__( 'Default' )];
        parent::__construct( $manager, $id, $args );
    }

    public function prepare_values( $id, $args = [] ) {
        $this->label        = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name         = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc         = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
        $this->default_value= isset( $args[0]['value'] )  ? date( "Y-m-d", strtotime( $args[0]['value'] ) )  :  "" ;
        $this->monday_first = isset( $args[0]['monday-first'] ) ? 1 : 0;
        $this->min_date     = isset( $args[0]['min-date'] ) ? date( "Y-m-d", strtotime( $args[0]['min-date'] ) ) : "today";
        $this->max_date     = isset( $args[0]['max-date'] ) ? date( "Y-m-d", strtotime( $args[0]['max-date'] ) ) : false;

        //generate attributes dynamically for parent tag
        
        if(isset( $args[0] )){
            $this->default_attributes = $this->prepare_default_attributes( $args[0] );
        }
    }

    /**
     * @internal
     */
    public function enqueue() {
        // wp_enqueue_style( 'flatpickr-css', DEVMONSTA_CORE . 'options/posts/controls/date-picker/assets/css/flatpickr.min.css' );
        // wp_enqueue_script( 'flatpickr', DEVMONSTA_CORE . 'options/posts/controls/date-picker/assets/js/flatpickr.js', ['jquery'] );
        // wp_enqueue_script( 'devm-date-picker-from-post', DEVMONSTA_CORE . 'options/posts/controls/date-picker/assets/js/script.js', ['jquery'] );
        // wp_enqueue_script( 'devm-customizer-date-picker', DEVMONSTA_CORE . 'options/customizer/controls/date-picker/assets/js/script.js', ['jquery', 'flatpickr', 'devm-date-picker-from-post'], time(), true );

        // $data                = [];
        // $data['mondayFirst'] = $this->monday_first ? 1 : 0;
        // $data['minDate']     = $this->min_date;
        // $data['maxDate']     = $this->max_date;
        // wp_localize_script( 'devm-customizer-date-picker', 'devm_date_picker_config', $data );
    }

    /**
     * @internal
     */
    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? $this->value() : $this->default_value;
        $this->render_content();
    }


    public function render_content() {
        $data                = [];
        $data['mondayFirst'] = $this->monday_first ? 1 : 0;
        $data['minDate']     = $this->min_date;
        $data['maxDate']     = $this->max_date;
        ?>
        <li <?php echo devm_render_markup( $this->default_attributes ); ?>>
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="devm-option-column right">
                <input data-config='<?php echo json_encode($data); ?>' <?php $this->link();?> type="text" name="<?php echo esc_attr( $this->name ); ?>"
                        class="devm-option-input devm-ctrl devm-option-input-date-picker"
                        value="<?php echo esc_attr( $this->value ); ?>"  data-value="<?php echo esc_html( $this->value ); ?>">
                <p class="devm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>

    <?php
    }

}
