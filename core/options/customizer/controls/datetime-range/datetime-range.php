<?php
namespace Devmonsta\Options\Customizer\Controls\DatetimeRange;

use Devmonsta\Options\Customizer\Structure;

class DatetimeRange extends Structure {

    public $label, $name, $desc, $default_attributes, $date_time_picker_default_data,
             $default_value, $value;

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
    public $type = 'datetime-range';

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
        $this->default_value           = ( isset( $args[0]['value']['from'] ) && isset( $args[0]['value']['to'] ) )
                                            ? ( date( "Y-m-d h:m a", strtotime( $args[0]['value']['from'] ) ) . " - " . date( "Y-m-d h:m a", strtotime( $args[0]['value']['to'] ) ) )
                                            : ( date( "Y-m-d h:m a" ) . " - " . date( "Y-m-d h:m a" ) );
        $date_time_picker_config       = isset( $args[0]['datetime-picker'] ) && is_array( $args[0]['datetime-picker'] ) ? $args[0]['datetime-picker'] : [];

        $date_format                   = isset( $date_time_picker_config['date-format'] ) && in_array( $date_time_picker_config['date-format'], $this->allowed_date_formats ) ? $date_time_picker_config['date-format'] : 'Y-m-d';
        $time_format                   = isset( $date_time_picker_config['time-format'] ) && in_array( $date_time_picker_config['time-format'], $this->allowed_time_formats ) ? $date_time_picker_config['time-format'] : 'H:i';
        $this->date_time_picker_default_data['format']      = $date_format . " " . $time_format;
        $this->date_time_picker_default_data['minDate']     = isset( $date_time_picker_config['min-date'] ) ? date( $this->date_time_picker_default_data['format'], strtotime( $date_time_picker_config['min-date'] ) ) : "today";
        $this->date_time_picker_default_data['maxDate']     = isset( $date_time_picker_config['max-date'] ) ? date( $this->date_time_picker_default_data['format'], strtotime( $date_time_picker_config['max-date'] ) ) : false;
        $this->date_time_picker_default_data['timepicker']  = ( $date_time_picker_config['timepicker'] ) ? 1 : 0;
        $this->date_time_picker_default_data['defaultTime'] = isset( $date_time_picker_config['default-time'] ) ? $date_time_picker_config['default-time'] : '12:00';
        
        //generate attributes dynamically for parent tag
        $this->default_attributes = $this->prepare_default_attributes( $args[0] );
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {
        wp_enqueue_style( 'flatpickr-css', DM_CORE . 'options/posts/controls/datetime-picker/assets/css/flatpickr.min.css' );
        wp_enqueue_script( 'flatpickr', DM_CORE . 'options/posts/controls/datetime-picker/assets/js/flatpickr.js', ['jquery'] );
        wp_enqueue_script( 'dm-date-time-range', DM_CORE . 'options/customizer/controls/datetime-range/assets/js/script.js', ['jquery', 'flatpickr'], false,true );
        wp_localize_script( 'dm-date-time-range', 'date_time_range_config', $this->date_time_picker_default_data );
    }

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
                <input type="text" class="dm-option-input dm-ctrl dm-option-input-datetime-range"
                    <?php $this->link(); ?> value="<?php echo esc_attr( $this->value ); ?>">
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>

    <?php
    }

}