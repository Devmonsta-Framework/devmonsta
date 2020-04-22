<?php

namespace Devmonsta\Options\Posts\Controls\DatetimeRange;

use Devmonsta\Options\Posts\Structure;

class DatetimeRange extends Structure {

    /**
     * @internal
     */
    public function init() {

    }

    /**
     * @internal
     */
    public function enqueue() {
        wp_enqueue_style( 'date-time-range', DM_CORE . 'options/posts/controls/datetime-range/assets/css/daterangepicker.css' );
        wp_enqueue_script( 'date-time-range', DM_CORE . 'options/posts/controls/datetime-range/assets/js/daterangepicker.js', ['jquery'] );
        wp_enqueue_script( 'date-time-range-moment', DM_CORE . 'options/posts/controls/datetime-range/assets/js/moment.min.js', ['jquery'] );
        wp_enqueue_script( 'dm-date-time-range', DM_CORE . 'options/posts/controls/datetime-range/assets/js/script.js', ['jquery', 'date-time-range'] );

        $date_time_range_config = $this->content['datetime-pickers'];
        $data['min_date']       = isset( $date_time_range_config['minDate'] ) ? date( "YYYY-MM-DD", strtotime($date_time_range_config['minDate']) ) : "";
        $data['max_date']       = isset( $date_time_range_config['maxDate'] ) ? date( "YYYY-MM-DD", strtotime($date_time_range_config['maxDate']) ) : "";
        $data['format']         = isset( $date_time_range_config['format'] ) ? $date_time_range_config['format'] : 'YYYY-MM-DD H:i';
        $data['datepicker']     = ( $date_time_range_config['datepicker'] ) ? "true" : "";
        $data['timepicker']     = ( $date_time_range_config['timepicker'] ) ? "true" : "";
        $data['time24hours']     = ( $date_time_range_config['time24hours'] ) ? "true" : "";

        wp_localize_script( 'dm-date-time-range', 'date_time_range_config', $data );
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
        $lable              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $default_attributes = "";

        if ( is_array( $attrs ) && !empty( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {
                $default_attributes .= $key . "='" . $val . "' ";
            }

        }

        ?>

        <div <?php echo esc_attr( $default_attributes ); ?>>
            <lable><?php echo esc_html( $lable ); ?> </lable>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <input type="text"
                    id="dm-datetime-range"
                    name="<?php echo esc_attr( $this->prefix . $name ); ?>"
                    value="<?php echo esc_attr( date( 'Y-m-d H:i', strtotime( $this->value ) ) ); ?>">
        </div<>
    <?php
}

}
