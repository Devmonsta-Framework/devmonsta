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
        wp_enqueue_style( 'dm-customizer-date-time-picker-css', plugin_dir_url( __FILE__ ) . '/assets/css/jquery.datetimepicker.min.css' );
        wp_enqueue_script( 'dm-customizer-date-time-picker', plugin_dir_url( __FILE__ ) . '/assets/js/jquery.datetimepicker.full.min.js', ['jquery'] );
        wp_enqueue_script( 'dm-customizer-date-time-picker-js', plugin_dir_url( __FILE__ ) . '/assets/js/script.js', ['jquery', 'dm-customizer-date-time-picker'], false, true );

        $date_time_picker_config               = $this->date_time_picker_config;
        $date_time_picker_data['format']       = isset( $date_time_picker_config['format'] ) ? $date_time_picker_config['format'] : 'Y-m-d H:i';
        $date_time_picker_data['min_date']     = isset( $date_time_picker_config['minDate'] ) ? date( $date_time_picker_data['format'], strtotime( $date_time_picker_config['minDate'] ) ) : date( $date_time_picker_data['format'] );
        $date_time_picker_data['max_date']     = isset( $date_time_picker_config['maxDate'] ) ? date( $date_time_picker_data['format'], strtotime( $date_time_picker_config['maxDate'] ) ) : "";
        $date_time_picker_data['datepicker']   = isset( $date_time_picker_config['datepicker'] ) ? $date_time_picker_config['datepicker'] : "";
        $date_time_picker_data['timepicker']   = isset( $date_time_picker_config['timepicker'] ) ? $date_time_picker_config['timepicker'] : "";
        $date_time_picker_data['default_time'] = isset( $date_time_picker_config['defaultTime'] ) ? $date_time_picker_config['defaultTime'] : '12:00';

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
