<?php
namespace Devmonsta\Options\Customizer\Controls\DatetimePicker;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class DatetimePicker extends \WP_Customize_Control {

    public $label, $name, $desc, $date_time_picker_config;

    private $allowed_date_formats = [
        'Y-m-d',
        'n/j/Y',
        'm/d/Y',
        'j/n/Y',
        'd/m/Y',
        'n-j-Y',
        'm-d-Y',
        'j-n-Y',
        'd-m-Y',
        'Y.m.d',
        'm.d.Y',
        'd.m.Y',
    ];
    private $allowed_time_formats = [
        'H:i',
        'h:i',
    ];

    /**
     * @access public
     * @var    string
     */
    public $type = 'datetime-picker';

    public $statuses;

    public function __construct( $manager, $id, $args = [] ) {

        $this->prepare_values( $id, $args );
        $this->statuses = ['' => __( 'Default' )];
        parent::__construct( $manager, $id, $args );
    }

    public function prepare_values( $id, $args = [] ) {
        $this->label                   = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name                    = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc                    = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
        $this->date_time_picker_config = isset( $args[0]['datetime-picker'] ) && is_array( $args[0]['datetime-picker'] ) ? $args[0]['datetime-picker'] : [];
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {
        wp_enqueue_style( 'flatpickr-css', DM_CORE . 'options/customizer/controls/datetime-picker/assets/css/flatpickr.min.css' );
        wp_enqueue_script( 'flatpickr', DM_CORE . 'options/customizer/controls/datetime-picker/assets/js/flatpickr.js', ['jquery'] );
        wp_enqueue_script( 'dm-customizer-date-time-picker', DM_CORE . 'options/customizer/controls/datetime-picker/assets/js/script.js', ['jquery', 'flatpickr'], false, true );
        $date_time_picker_data                = [];
        $date_format                          = isset( $this->date_time_picker_config['date-format'] ) && in_array( $this->date_time_picker_config['date-format'], $this->allowed_date_formats ) ? $this->date_time_picker_config['date-format'] : 'Y-m-d';
        $time_format                          = isset( $this->date_time_picker_config['time-format'] ) && in_array( $this->date_time_picker_config['time-format'], $this->allowed_time_formats ) ? $this->date_time_picker_config['time-format'] : 'H:i';
        $date_time_picker_data['format']      = $date_format . " " . $time_format;
        $date_time_picker_data['minDate']     = isset( $this->date_time_picker_config['min-date'] ) ? date( $date_time_picker_data['format'], strtotime( $this->date_time_picker_config['min-date'] ) ) : "today";
        $date_time_picker_data['maxDate']     = isset( $this->date_time_picker_config['max-date'] ) ? date( $date_time_picker_data['format'], strtotime( $this->date_time_picker_config['max-date'] ) ) : false;
        $date_time_picker_data['timepicker']  = ( $this->date_time_picker_config['timepicker'] ) ? 1 : 0;
        $date_time_picker_data['defaultTime'] = isset( $this->date_time_picker_config['default-time'] ) ? $this->date_time_picker_config['default-time'] : '12:00';
        wp_localize_script( 'dm-customizer-date-time-picker', 'date_time_picker_config', $date_time_picker_data );
    
    }

    public function render() {
        $this->render_content();
    }

    public function render_content() {
        ?>

        <div>
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="dm-option-column right">
                <input <?php $this->link();?>
                    type="text"
                    id="dm-datetime-picker"
                    class="dm-option-input dm-option-input-datetime-picker"
                    name="<?php echo esc_attr( $this->name ); ?>"
                    value="<?php echo esc_attr( $this->value() ); ?>">
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </div>

    <?php
    }

}
