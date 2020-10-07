<?php
namespace Devmonsta\Options\Customizer\Controls\DatetimePicker;

use Devmonsta\Options\Customizer\Structure;

class DatetimePicker extends Structure {

    public $label, $name, $desc, $date_time_picker_config,
            $default_attributes, $default_value, $value;

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
    public $type = 'datetime-picker';

    public $statuses;

    public function __construct( $manager, $id, $args = [] ) {

        $this->prepare_values( $id, $args );
        $this->statuses = ['' =>esc_html__( 'Default' )];
        parent::__construct( $manager, $id, $args );
    }

    public function prepare_values( $id, $args = [] ) {
        $this->label                   = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name                    = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc                    = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
        $this->default_value           = isset( $args[0]['value'] ) ? date( "Y-m-d H:i", strtotime( $args[0]['value'] ) ): "";
        $this->date_time_picker_config = isset( $args[0]['datetime-picker'] ) && is_array( $args[0]['datetime-picker'] ) ? $args[0]['datetime-picker'] : [];

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
        // if ( !wp_script_is( 'flatpickr', 'enqueued' ) ) {
        //     wp_enqueue_script( 'flatpickr', DEVMONSTA_CORE . 'options/posts/controls/datetime-picker/assets/js/flatpickr.js', ['jquery'], false, true );
        // }
        // wp_enqueue_script( 'devm-date-time-picker-from-post', DEVMONSTA_CORE . 'options/posts/controls/datetime-picker/assets/js/script.js', ['jquery'] );
        // wp_enqueue_script( 'devm-customizer-date-time-picker', DEVMONSTA_CORE . 'options/customizer/controls/datetime-picker/assets/js/script.js', ['jquery', 'flatpickr', 'devm-date-time-picker-from-post'], false, true );

        // $date_time_picker_data                = [];
        // $date_time_picker_data['format']      = isset( $this->date_time_picker_config['date-format'] ) && in_array( strtolower( $this->date_time_picker_config['date-format'] ), $this->allowed_date_formats ) ? $this->date_time_picker_config['date-format'] : 'Y-m-d H:i';
        // $date_time_picker_data['is24Format']  = isset( $this->date_time_picker_config['time-24'] ) && $this->date_time_picker_config['time-24'] ? 1 : 0;
        // $date_time_picker_data['minDate']     = isset( $this->date_time_picker_config['min-date'] ) ? date( $date_time_picker_data['format'], strtotime( $this->date_time_picker_config['min-date'] ) ) : "today";
        // $date_time_picker_data['maxDate']     = isset( $this->date_time_picker_config['max-date'] ) ? date( $date_time_picker_data['format'], strtotime( $this->date_time_picker_config['max-date'] ) ) : "";
        // $date_time_picker_data['timepicker']  = ( $this->date_time_picker_config['timepicker'] ) ? 1 : 0;
        // $date_time_picker_data['defaultTime'] = isset( $this->date_time_picker_config['default-time'] ) && preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $this->date_time_picker_config['default-time'])? $this->date_time_picker_config['default-time'] : '12:00';
        // wp_localize_script( 'devm-customizer-date-time-picker', 'date_time_picker_config', $date_time_picker_data );
    }

    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? $this->value() : $this->default_value;
        $this->render_content();
    }

    public function render_content() {
        $date_time_picker_data                = [];
        $date_time_picker_data['format']      = isset( $this->date_time_picker_config['date-format'] ) && in_array( strtolower( $this->date_time_picker_config['date-format'] ), $this->allowed_date_formats ) ? $this->date_time_picker_config['date-format'] : 'Y-m-d H:i';
        $date_time_picker_data['is24Format']  = isset( $this->date_time_picker_config['time-24'] ) && $this->date_time_picker_config['time-24'] ? 1 : 0;
        $date_time_picker_data['minDate']     = isset( $this->date_time_picker_config['min-date'] ) ? date( $date_time_picker_data['format'], strtotime( $this->date_time_picker_config['min-date'] ) ) : "today";
        $date_time_picker_data['maxDate']     = isset( $this->date_time_picker_config['max-date'] ) ? date( $date_time_picker_data['format'], strtotime( $this->date_time_picker_config['max-date'] ) ) : "";
        $date_time_picker_data['timepicker']  = ( $this->date_time_picker_config['timepicker'] ) ? 1 : 0;
        $date_time_picker_data['defaultTime'] = isset( $this->date_time_picker_config['default-time'] ) && preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $this->date_time_picker_config['default-time'])? $this->date_time_picker_config['default-time'] : '12:00';
        ?>
        <li <?php echo devm_render_markup( $this->default_attributes ); ?>>
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="devm-option-column right">
                <input <?php $this->link();?> name="<?php echo esc_attr( $this->name ); ?>" type="text" class="devm-option-input devm-ctrl devm-option-input-datetime-picker" data-config='<?php echo json_encode($date_time_picker_data); ?>'
                    value="<?php echo esc_attr( $this->value ); ?>"  data-value="<?php echo esc_html( $this->value ); ?>">
                <p class="devm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>

    <?php
    }

}
