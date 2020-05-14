<?php

namespace Devmonsta\Options\Posts\Controls\DatetimeRange;

use Devmonsta\Options\Posts\Structure;

class DatetimeRange extends Structure {

    protected $current_screen;

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
        wp_enqueue_script( 'dm-date-time-range', DM_CORE . 'options/posts/controls/datetime-range/assets/js/script.js', ['jquery'] );

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
        $content      = $this->content;
        $default_time = ( isset( $content['value']['from'] ) && isset( $content['value']['to'] ) )
        ? ( date( "Y-m-d h:m a", strtotime( $content['value']['from'] ) ) . " - " . date( "Y-m-d h:m a", strtotime( $content['value']['to'] ) ) )
        : ( date( "Y-m-d h:m a" ) . " - " . date( "Y-m-d h:m a" ) );
        global $post;

        $this->value = (  ( $this->current_screen == "post" )
            && ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
            && ( "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
        ? get_post_meta( $post->ID, $this->prefix . $content['name'], true )
        : $default_time;

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $prefix             = 'devmonsta_';
        $name               = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
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

        $class_attributes = "class='dm-option form-field $dynamic_classes'";
        $default_attributes .= $class_attributes;

        ?>
        <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
            </div>

            <div class="dm-option-column right">
                <input type="text"
                        id="dm-datetime-range"
                        class="dm-option-input dm-option-input-datetime-range"
                        name="<?php echo esc_attr( $name ); ?>"
                        value="<?php echo esc_attr( $this->value ); ?>">
                <p class="dm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
    <?php
}

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

    public function edit_fields( $term, $taxonomy ) {
        $this->enqueue_date_time_range_scripts();
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $prefix             = 'devmonsta_';
        $name               = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $value              = get_term_meta( $term->term_id, $name, true );
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

        $class_attributes = "class='dm-option term-group-wrap $dynamic_classes'";
        $default_attributes .= $class_attributes;

        ?>

<tr <?php echo dm_render_markup( $default_attributes ); ?> >
    <th scope="row">
        <label class="dm-option-label"><?php echo esc_html( $label ); ?></label>
    </th>
    <td>
        <input type="text"
                    id="dm-datetime-range"
                    name="<?php echo esc_attr( $name ); ?>"
                    value="<?php echo esc_attr( $value ); ?>">
        <br><small class="dm-option-desc">(<?php echo esc_html( $desc ); ?> )</small>
    </td>
</tr>
<?php
}

}
