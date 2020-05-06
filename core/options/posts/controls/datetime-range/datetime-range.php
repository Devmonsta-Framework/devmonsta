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
        $data['min_date']       = isset( $date_time_range_config['minDate'] ) ? date( "YYYY-MM-DD", strtotime( $date_time_range_config['minDate'] ) ) : date( "YYYY-MM-DD" );
        $data['max_date']       = isset( $date_time_range_config['maxDate'] ) ? date( "YYYY-MM-DD", strtotime( $date_time_range_config['maxDate'] ) ) : "";
        $data['format']         = isset( $date_time_range_config['format'] ) ? $date_time_range_config['format'] : 'YYYY-MM-DD hh:mm a';
        $data['datepicker']     = ( $date_time_range_config['datepicker'] ) ? "true" : "";
        $data['timepicker']     = ( $date_time_range_config['timepicker'] ) ? "true" : "";
        $data['time24hours']    = ( $date_time_range_config['time24hours'] ) ? "true" : "";
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
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $default_attributes = "";
        $dynamic_classes    = "";

        if ( is_array( $attrs ) && !empty( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {

                if ( $key == "class" ) {
                    $dynamic_classes .= $val . " ";
                } else {
                    $default_attributes .= $key . "='" . $val . "' ";
                }

            }

        }

        $class_attributes = "class='dm-option $dynamic_classes'";
        $default_attributes .= $class_attributes;

        ?>
        <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <label class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
            <div><small class="dm-option-desc"><?php echo esc_html( $desc ); ?> </small></div>
            <input type="text"
                    id="dm-datetime-range"
                    name="<?php echo esc_attr( $this->prefix . $name ); ?>"
                    value="<?php echo esc_attr( $this->value ); ?>">
        </div<>
    <?php
}

}
