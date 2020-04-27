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
        wp_enqueue_style( 'date-time-picker', DM_CORE . 'options/posts/controls/datetime-picker/assets/css/jquery.datetimepicker.min.css' );
        wp_enqueue_script( 'date-time-picker', DM_CORE . 'options/posts/controls/datetime-picker/assets/js/jquery.datetimepicker.full.min.js', array('jquery') );
        wp_enqueue_script( 'dm-date-time-picker', DM_CORE . 'options/posts/controls/datetime-picker/assets/js/script.js', array('jquery', 'date-time-picker') );

        $date_time_picker_config = $this->content['datetime-picker'];
        $data['min_date']        = isset( $date_time_picker_config['minDate'] ) ? $date_time_picker_config['minDate'] : "";
        $data['max_date']        = isset( $date_time_picker_config['maxDate'] ) ? $date_time_picker_config['maxDate'] : "";
        $data['format']          = isset( $date_time_picker_config['format'] ) ? $date_time_picker_config['format'] : 'Y-m-d H:i';
        $data['datepicker']      = isset( $date_time_picker_config['datepicker'] ) ? $date_time_picker_config['datepicker'] : false;
        $data['timepicker']      = isset( $date_time_picker_config['timepicker'] ) ? $date_time_picker_config['timepicker'] : false;
        $data['default_time']    = isset( $date_time_picker_config['defaultTime'] ) ? $date_time_picker_config['defaultTime'] : '12:00';
        
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
        $name  = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc  = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $default_attributes = "";

        if ( is_array( $attrs ) && !empty( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {
                $default_attributes .= $key . "='" . $val . "' ";
            }

        }

        ?>

        <div <?php echo esc_attr($default_attributes);?>>
            <lable><?php echo esc_html( $lable ); ?> </lable>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <input type="text"
                    id="dm-datetime-picker"
                    name="<?php echo esc_attr( $this->prefix . $name ); ?>"
                    value="<?php echo esc_attr( date( 'Y-m-d H:i', strtotime($this->value)  ) ); ?>">
        </div<>
    <?php
}

}
