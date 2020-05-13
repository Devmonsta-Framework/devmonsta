<?php
namespace Devmonsta\Options\Customizer\Controls\DatetimePicker;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class DatetimePicker extends \WP_Customize_Control {

    public $label, $name, $desc, $date_time_picker_config;

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

        var_dump( $args[0] );
        $this->label                   = $args[0]['label'];
        $this->name                    = $args[0]['id'];
        $this->desc                    = $args[0]['desc'];
        $this->date_time_picker_config = $args[0]['datetime-picker'];
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
        $date_time_picker_data['min_date']     = ( $date_time_picker_config['minDate'] ) ? date( $date_time_picker_data['format'], strtotime( $date_time_picker_config['minDate'] ) ) : date( $date_time_picker_data['format'] );
        $date_time_picker_data['max_date']     = ( $date_time_picker_config['maxDate'] ) ? date( $date_time_picker_data['format'], strtotime( $date_time_picker_config['maxDate'] ) ) : "";
        $date_time_picker_data['datepicker']   = isset( $date_time_picker_config['datepicker'] ) ? $date_time_picker_config['datepicker'] : "";
        $date_time_picker_data['timepicker']   = isset( $date_time_picker_config['timepicker'] ) ? $date_time_picker_config['timepicker'] : "";
        $date_time_picker_data['default_time'] = isset( $date_time_picker_config['defaultTime'] ) ? $date_time_picker_config['defaultTime'] : '12:00';

        wp_localize_script( 'dm-customizer-date-time-picker', 'date_time_picker_config', $date_time_picker_data );
    }

    public function enqueue_date_time_picker_scripts() {
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
                    class="dm-option-input dm-datetime-picker"
                    name="<?php echo esc_attr( $this->name ); ?>"
                    value="<?php echo esc_attr( $this->value() ); ?>">
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </div>

    <?php
}

}
