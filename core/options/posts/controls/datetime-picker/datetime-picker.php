<?php

namespace Devmonsta\Options\Posts\Controls\DatetimePicker;

use Devmonsta\Options\Posts\Structure;

class DatetimePicker extends Structure {

    /**
     * @internal
     */
    public function init() {

    }

    /**
     * @internal
     */
    public function enqueue() {

        wp_enqueue_script( 'dm-date-time-picker', plugins_url( 'datetime-picker/assets/js/script.js', dirname( __FILE__ ) ) );

        $date_time_picker_config = $this->content['datetime-picker'];
        $data['min_date'] = isset( $date_time_picker_config['minDate'] ) ? $date_time_picker_config['minDate'] : date( 'd-m-Y' );
        $data['max_date'] = isset( $date_time_picker_config['maxDate'] ) ? $date_time_picker_config['maxDate'] : '';
        $data['format'] = isset( $date_time_picker_config['format'] ) ? $date_time_picker_config['format'] : 'Y-m-d H:i';
        $data['datepicker'] = isset( $date_time_picker_config['datepicker'] ) ? $date_time_picker_config['datepicker'] : false;
        $data['timepicker'] = isset( $date_time_picker_config['timepicker'] ) ? $date_time_picker_config['timepicker'] : false;
        $data['default_time'] = isset( $date_time_picker_config['defaultTime'] ) ? $date_time_picker_config['defaultTime'] : '12:00';

        wp_localize_script( 'dm-date-time-picker', 'date_time_picker_config', $data );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;
        $this->value = !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ?
        get_post_meta( $post->ID, $this->prefix . $content['name'], true )
        : $content['value'];
        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $lable = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        ?>
       <div  <?php

                if ( is_array( $attrs ) ) {

                    foreach ( $attrs as $key => $val ) {
                        echo esc_html( $key ) . "='" . esc_attr( $val ) . "' ";
                    }

                }

                ?>>
            <lable><?php echo esc_html( $lable ); ?> </lable>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <input type="text"
                    id="dm-datetime-picker"
                    name="<?php echo esc_html( $this->prefix . $name ); ?>"
                    value="<?php echo esc_html( date( 'Y-m-d H:i', $this->value ) ); ?>">
        </div<>
    <?php
}

}
