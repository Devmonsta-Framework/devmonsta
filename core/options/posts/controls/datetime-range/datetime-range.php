<?php

namespace Devmonsta\Options\Posts\Controls\DatetimeRange;

use Devmonsta\Options\Posts\Structure;

class DatetimeRange extends Structure {

    protected $current_screen;

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
        'h:i'
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
            $this->enqueue_date_time_range_scripts();
        } elseif ( $this->current_screen == "taxonomy" ) {
            add_action( 'init', [$this, 'enqueue_date_time_range_scripts'] );
        }

    }

    public function enqueue_date_time_range_scripts() {

        wp_enqueue_style( 'flatpickr-css', DM_CORE . 'options/posts/controls/datetime-picker/assets/css/flatpickr.min.css' );
        wp_enqueue_script( 'flatpickr', DM_CORE . 'options/posts/controls/datetime-picker/assets/js/flatpickr.js', ['jquery'] );
        wp_enqueue_script( 'dm-date-time-range', DM_CORE . 'options/posts/controls/datetime-range/assets/js/script.js', ['jquery'], time() );

        $date_time_range_config               = $this->content['datetime-pickers'];
        $date_format                           = isset( $date_time_range_config['date-format'] ) && in_array($date_time_range_config['date-format'], $this->allowed_date_formats) ? $date_time_range_config['date-format'] : 'Y-m-d';
        $time_format                           = isset( $date_time_range_config['time-format'] ) && in_array($date_time_range_config['time-format'], $this->allowed_time_formats) ? $date_time_range_config['time-format'] : 'H:i';
        $date_time_range_data['format']       = $date_format . " " . $time_format;
        $date_time_range_data['minDate']      = isset( $date_time_range_config['min-date'] ) ? date( $date_time_range_data['format'], strtotime($date_time_range_config['min-date'])) : "today";
        $date_time_range_data['maxDate']      = isset( $date_time_range_config['max-date'] ) ? date( $date_time_range_data['format'], strtotime($date_time_range_config['max-date'])) : false;
        $date_time_range_data['timepicker']   = ( $date_time_range_config['timepicker'] ) ? 1 : 0;
        $date_time_range_data['defaultTime'] = isset( $date_time_range_config['defaultTime'] ) ? $date_time_range_config['defaultTime'] : '12:00';

        wp_localize_script( 'dm-date-time-range', 'date_time_range_config', $date_time_range_config );

    }

    /**
     * @internal
     */
    public function render() {
        $content       = $this->content;
        $default_value = ( isset( $content['value']['from'] ) && isset( $content['value']['to'] ) )
                        ? ( date( "Y-m-d H:i", strtotime( $content['value']['from'] ) ) . " to " . date( "Y-m-d H:i", strtotime( $content['value']['to'] ) ) )
                        : ( date( "Y-m-d H:i" ) . " to " . date( "Y-m-d H:i" ) );
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
        $this->enqueue_date_time_range_scripts();
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
        ?>
        <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
            </div>

            <div class="dm-option-column right">
                <input type="text"
                        class="dm-option-input dm-ctrl dm-option-input-datetime-range"
                        name="<?php echo esc_attr( $name ); ?>"
                        value="<?php echo esc_attr( $value ); ?>">
                <p class="dm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
    
    <?php
    }

}
