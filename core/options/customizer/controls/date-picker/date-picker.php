<?php
namespace Devmonsta\Options\Customizer\Controls\DatePicker;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class DatePicker extends \WP_Customize_Control {

    public $label, $name, $desc, $monday_first, $min_date, $max_date;

    /**
     * @access public
     * @var    string
     */
    public $type = 'date-picker';

    public $statuses;

    public function __construct( $manager, $id, $args = [] ) {

        $this->prepare_values( $id, $args );
        $this->statuses = ['' => __( 'Default' )];
        parent::__construct( $manager, $id, $args );
    }

    public function prepare_values( $id, $args = [] ) {
        $this->label        = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name         = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc         = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
        $this->monday_first = isset( $args[0]['monday-first'] ) ? 1 : 0;
        $this->min_date     = isset( $args[0]['min-date'] ) ? date( "Y-m-d", strtotime( $args[0]['min-date'] ) ) : "today";
        $this->max_date     = isset( $args[0]['max-date'] ) ? date( "Y-m-d", strtotime( $args[0]['max-date'] ) ) : false;
    }

    /**
     * @internal
     */
    public function enqueue() {
        wp_enqueue_style( 'flatpickr-css', DM_CORE . 'options/posts/controls/date-picker/assets/css/flatpickr.min.css' );
        wp_enqueue_script( 'flatpickr', DM_CORE . 'options/posts/controls/date-picker/assets/js/flatpickr.js', ['jquery'] );
        wp_enqueue_script( 'dm-customizer-date-picker', DM_CORE . 'options/customizer/controls/date-picker/assets/js/script.js', ['jquery'] );

        $data                = [];
        $data['mondayFirst'] = $this->monday_first ? 1 : 0;
        $data['minDate']     = $this->min_date;
        $data['maxDate']     = $this->max_date;
        wp_localize_script( 'dm-customizer-date-picker', 'dm_date_picker_config', $data );
    }

    /**
     * @internal
     */
    public function render() {
        $this->render_content();
    }

    
    public function render_content() {
        ?>

        <li  class="dm-option">
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="dm-option-column right">
                <input <?php $this->link();?> type="date" name="<?php echo esc_attr( $this->name ); ?>"
                        class="dm-option-input dm-ctrl dm-option-input-date-picker"
                        value="<?php echo esc_attr( $this->value() ); ?>">
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>

    <?php
    }

}
