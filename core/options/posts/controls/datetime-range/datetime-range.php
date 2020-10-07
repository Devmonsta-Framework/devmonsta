<?php

namespace Devmonsta\Options\Posts\Controls\DatetimeRange;

use Devmonsta\Options\Posts\Structure;

class DatetimeRange extends Structure {

    protected $current_screen, $date_time_range_format;

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
     * @internal
     */
    public function init() {

    }

    /**
     * @internal
     */
    public function enqueue( $meta_owner ) {
        $this->current_screen = $meta_owner;
    }

    /**
     * @internal
     */
    public function render() {
        $content       = $this->content;
        $this->date_time_range_format = isset( $content['datetime-picker']['date-format'] ) && in_array( strtolower(  $content['datetime-picker']['date-format'] ), $this->allowed_date_formats ) ?  $content['datetime-picker']['date-format'] : 'Y-m-d H:i';
        $default_value = ( isset( $content['value']['from'] ) && isset( $content['value']['to'] ) )
                        ? ( date( $this->date_time_range_format, strtotime( $content['value']['from'] ) ) . " - " . date( $this->date_time_range_format, strtotime( $content['value']['to'] ) ) )
                        : "";
        global $post;
        $this->value = (  ( $this->current_screen == "post" )
                        && ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
                        && ( "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
                        ? get_post_meta( $post->ID, $this->prefix . $content['name'], true )
                        : $default_value;

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';

        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $this->value, $desc );
    }

    /**
     * @internal
     */
    public function columns() {
        $visible = false;
        $content = $this->content;
        add_filter( 'manage_edit-' . $this->taxonomy . '_columns',
            function ( $columns ) use ( $content, $visible ) {

                $visible = ( isset( $content['show_in_table'] ) && $content['show_in_table'] === true ) ? true : false;

                if ( $visible ) {
                    $columns[$content['name']] =esc_html__( $content['label'], 'devmonsta' );
                }

                return $columns;
            } );

        $cc = $content;
        add_filter( 'manage_' . $this->taxonomy . '_custom_column',
            function ( $content, $column_name, $term_id ) use ( $cc ) {
                if ( $column_name == $cc['name'] ) {
                    echo esc_html( get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) );
                }

                return $content;
            }, 10, 3 );

    }

    /**
     * @internal
     */
    public function edit_fields( $term, $taxonomy ) {
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $value              = (  ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) && ( "" != get_term_meta( $term->term_id, $name, true ) ) ) ? get_term_meta( $term->term_id, $name, true ) : "";

        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $value, $desc );
    }

    /**
     * Renders markup with given attributes
     *
     * @param [type] $default_attributes
     * @param [type] $label
     * @param [type] $name
     * @param [type] $value
     * @param [type] $desc
     * @return void
     */
    public function generate_markup( $default_attributes, $label, $name, $value, $desc ) {
        $date_time_range_config                = $this->content['datetime-picker'];
        $date_time_range_data['format']        = isset( $date_time_range_config['date-format'] ) && in_array( strtolower(  $date_time_range_config['date-format'] ), $this->allowed_date_formats ) ?  $date_time_range_config['date-format'] : 'Y-m-d H:i';
        $date_time_range_data['is24Format']    = isset( $date_time_range_config['time-24'] ) && $date_time_range_config['time-24'] ? 1 : 0;
        $date_time_range_data['minDate']       = isset( $date_time_range_config['min-date'] ) ? date( $date_time_range_data['format'], strtotime($date_time_range_config['min-date'])) : "today";
        $date_time_range_data['maxDate']       = isset( $date_time_range_config['max-date'] ) ? date( $date_time_range_data['format'], strtotime($date_time_range_config['max-date'])) : false;
        $date_time_range_data['timepicker']    = ( $date_time_range_config['timepicker'] ) ? 1 : 0;
        $date_time_range_data['defaultTime']   = isset( $date_time_range_config['defaultTime'] ) && preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $date_time_range_config['defaultTime']) ? $date_time_range_config['defaultTime'] : '12:00';
        ?>
        <div <?php echo devm_render_markup( $default_attributes ); ?> >
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $label ); ?> </label>
            </div>

            <div class="devm-option-column right">
                <input type="text" data-config='<?php echo json_encode($date_time_range_data); ?>'
                        class="devm-option-input devm-ctrl devm-option-input-datetime-range"
                        name="<?php echo esc_attr( $name ); ?>"
                        value="<?php echo esc_attr( $value ); ?>">
                <p class="devm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
        <?php
    }

}
