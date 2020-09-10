<?php
namespace Devmonsta\Options\Customizer\Controls\DatetimeRange;

use Devmonsta\Options\Customizer\Structure;

class DatetimeRange extends Structure {

    public $label, $name, $desc, $default_attributes, $date_time_range_default_data,
    $default_value, $value;

    private $allowed_date_formats = [
        'y-m-d h:i',
        'n/j/y h:i',
        'm/d/y h:i',
        'j/n/y h:i',
        'd/m/y h:i',
        'n-j-y h:i',
        'm-d-y h:i',
        'j-n-y h:i',
        'd-m-y h:i',
        'y.m.d h:i',
        'm.d.y h:i',
        'd.m.y h:i',
    ];

    /**
     * @access public
     * @var    string
     */
    public $type = 'datetime-range';

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
        $this->default_value = ( isset( $args[0]['value']['from'] ) && isset( $args[0]['value']['to'] ) )
                                ? ( date( "Y-m-d H:i", strtotime( $args[0]['value']['from'] ) ) . " to " . date( "Y-m-d H:i", strtotime( $args[0]['value']['to'] ) ) )
                                : "";
        $date_time_picker_config = isset( $args[0]['datetime-picker'] ) && is_array( $args[0]['datetime-picker'] ) ? $args[0]['datetime-picker'] : [];

        $this->date_time_range_default_data['format']      = isset( $date_time_picker_config['date-format'] ) && in_array( strtolower(  $date_time_picker_config['date-format'] ), $this->allowed_date_formats ) ?  $date_time_picker_config['date-format'] : 'Y-m-d H:i';
        $this->date_time_range_default_data['is24Format']  = isset( $date_time_picker_config['time-24'] ) &&  $date_time_picker_config['time-24'] ? 1 : 0;
        $this->date_time_range_default_data['minDate']     = isset( $date_time_picker_config['min-date'] ) ? date( $this->date_time_range_default_data['format'], strtotime( $date_time_picker_config['min-date'] ) ) : "today";
        $this->date_time_range_default_data['maxDate']     = isset( $date_time_picker_config['max-date'] ) ? date( $this->date_time_range_default_data['format'], strtotime( $date_time_picker_config['max-date'] ) ) : false;
        $this->date_time_range_default_data['timepicker']  = ( $date_time_picker_config['timepicker'] ) ? 1 : 0;
        $this->date_time_range_default_data['defaultTime'] =  isset( $date_time_picker_config['defaultTime'] ) && preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $date_time_picker_config['defaultTime']) ? $date_time_picker_config['defaultTime'] : '12:00';

        //generate attributes dynamically for parent tag
        if(isset( $args[0] )){
            $this->default_attributes = $this->prepare_default_attributes( $args[0] );
        }
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {
        // wp_enqueue_style( 'flatpickr-css', DEVMONSTA_CORE . 'options/posts/controls/datetime-picker/assets/css/flatpickr.min.css' );
        // wp_enqueue_script( 'flatpickr', DEVMONSTA_CORE . 'options/posts/controls/datetime-picker/assets/js/flatpickr.js', ['jquery'] );
        // wp_enqueue_script( 'devm-date-time-range-from-post', DEVMONSTA_CORE . 'options/posts/controls/datetime-range/assets/js/script.js', ['jquery'] );
        // wp_enqueue_script( 'devm-date-time-range', DEVMONSTA_CORE . 'options/customizer/controls/datetime-range/assets/js/script.js', ['jquery', 'flatpickr', 'devm-date-time-range-from-post'], false, true );
        // wp_localize_script( 'devm-date-time-range', 'date_time_range_config', $this->date_time_range_default_data );
    }

    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? $this->value() : $this->default_value;
        $this->render_content();
    }

    public function render_content() {
        ?>
        <li <?php echo devm_render_markup( $this->default_attributes ); ?>>
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="devm-option-column right">
                <input type="text" class="devm-option-input devm-ctrl devm-option-input-datetime-range" name="<?php echo esc_attr( $this->name ); ?>" data-config='<?php echo json_encode($this->date_time_range_default_data); ?>'
                    <?php $this->link();?> value="<?php echo esc_attr( $this->default_value ); ?>" data-value="<?php echo esc_html( $this->value ); ?>">
                <p class="devm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>

    <?php
}

}
