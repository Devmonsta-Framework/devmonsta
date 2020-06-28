<?php

namespace Devmonsta\Options\Posts\Controls\DatetimePicker;

use Devmonsta\Options\Posts\Structure;

class DatetimePicker extends Structure {

    protected $current_screen;

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

        if ( $this->current_screen == "post" ) {

            $this->enqueue_date_time_picker_scripts();

        } elseif ( $this->current_screen == "taxonomy" ) {
            add_action( 'init', [$this, 'enqueue_date_time_picker_scripts'] );

        }

    }

    public function enqueue_date_time_picker_scripts() {
        wp_enqueue_style( 'flatpickr-css', DM_CORE . 'options/posts/controls/datetime-picker/assets/css/flatpickr.min.css' );
        wp_enqueue_script( 'flatpickr', DM_CORE . 'options/posts/controls/datetime-picker/assets/js/flatpickr.js', ['jquery'] );
        wp_enqueue_script( 'dm-date-time-picker', DM_CORE . 'options/posts/controls/datetime-picker/assets/js/script.js', ['jquery'] );

        $date_time_picker_config               = $this->content['datetime-picker'];
        $date_time_picker_data['format']       = isset( $date_time_picker_config['date-format'] ) && in_array( strtolower( $date_time_picker_config['date-format'] ), $this->allowed_date_formats ) ? $date_time_picker_config['date-format'] : 'Y-m-d H:i';
        $date_time_picker_data['abc']   = isset( $date_time_picker_config['time-24'] ) && $date_time_picker_config['time-24'] ? 1 : 0;
        $date_time_picker_data['minDate']      = isset( $date_time_picker_config['min-date'] ) ? date("Y-m-d H:i", strtotime($date_time_picker_config['min-date'])) : "today";
        $date_time_picker_data['maxDate']      = isset( $date_time_picker_config['max-date'] ) ? date("Y-m-d H:i", strtotime($date_time_picker_config['max-date'])) : false;
        $date_time_picker_data['timepicker']   = ( $date_time_picker_config['timepicker'] ) ? 1 : 0;
        $date_time_picker_data['defaultTime']  =  isset( $date_time_picker_config['defaultTime'] ) && preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $date_time_picker_config['defaultTime']) ? $date_time_picker_config['defaultTime'] : '12:00';

        wp_localize_script( 'dm-date-time-picker', 'date_time_picker_config', $date_time_picker_data );

    }

    /**
     * @internal
     */
    public function render() {
        $content       = $this->content;
        $default_value = isset( $content['value'] ) ? date('Y-m-d H:i', strtotime($content['value'])) : "";
        global $post;
        $this->value = (  ( $this->current_screen == "post" )
                            && ( "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                            && !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) ?
                        get_post_meta( $post->ID, $this->prefix . $content['name'], true )
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
                    $columns[$content['name']] = __( $content['label'], 'devmonsta' );
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

        $this->enqueue_date_time_picker_scripts();
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $value              = ( !is_null( get_term_meta( $term->term_id, $name, true ) ) && "" != get_term_meta( $term->term_id, $name, true ) ) ? get_term_meta( $term->term_id, $name, true ) : "";
                
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
    ?>
        <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
            </div>

            <div class="dm-option-column right">
                <input type="text"
                    class="dm-option-input dm-ctrl dm-option-input-datetime-picker"
                    name="<?php echo esc_attr( $name ); ?>"
                    value="<?php echo esc_attr( $value ); ?>">
                <p class="dm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
    <?php
    }

}
