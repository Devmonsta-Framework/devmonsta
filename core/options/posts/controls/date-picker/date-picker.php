<?php

namespace Devmonsta\Options\Posts\Controls\DatePicker;

use Devmonsta\Options\Posts\Structure;

class DatePicker extends Structure {

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

        // add_action( 'init', [$this, 'enqueue_date_time_picker_scripts'] );
        // add_action( 'admin_init', [$this, 'enqueue_date_time_picker_scripts'] );
        // add_action( 'admin_enqueue_scripts', [$this, 'enqueue_date_time_picker_scripts'] );

        if ( $this->current_screen == "post" ) {
            $this->enqueue_date_time_picker_scripts();
        } elseif ( $this->current_screen == "taxonomy" ) {
            add_action( 'init', [$this, 'enqueue_date_time_picker_scripts'] );
        }

    }

    public function enqueue_date_time_picker_scripts() {
        wp_enqueue_style( 'flatpickr-css', DM_CORE . 'options/posts/controls/date-picker/assets/css/flatpickr.min.css' );
        wp_enqueue_script( 'flatpickr', DM_CORE . 'options/posts/controls/date-picker/assets/js/flatpickr.js', ['jquery'] );
        wp_enqueue_script( 'dm-date-picker', DM_CORE . 'options/posts/controls/date-picker/assets/js/script.js', ['jquery'] );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;

        $default_value = isset( $content['value'] ) ? $content['value'] : "";

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
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $min_date           = isset( $this->content['min-date'] ) ? $this->content['min-date'] : date( 'd-m-Y' );
        $max_date           = isset( $this->content['max-date'] ) ? $this->content['max-date'] : '';
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
                <input type="date" name="<?php echo esc_attr( $name ); ?>"
                    class="dm-option-input dm-option-input-date-picker"
                    value="<?php echo esc_attr( $this->value ); ?>"
                    min="<?php echo esc_attr( $min_date ) ?>" max="<?php echo esc_attr( $max_date ) ?>">
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
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $value              = ( !is_null( get_term_meta( $term->term_id, $name, true ) ) && "" != get_term_meta( $term->term_id, $name, true ) ) ? get_term_meta( $term->term_id, $name, true ) : "";
        $min_date           = isset( $this->content['min-date'] ) ? $this->content['min-date'] : date( 'd-m-Y' );
        $max_date           = isset( $this->content['max-date'] ) ? $this->content['max-date'] : '';
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
            <input type="date" name="<?php echo esc_attr( $name ); ?>"
                    value="<?php echo esc_attr( $value ); ?>"
                    min="<?php echo esc_attr( $min_date ) ?>" max="<?php echo esc_attr( $max_date ) ?>">
            <br><small class="dm-option-desc">(<?php echo esc_html( $desc ); ?> )</small>
        </td>
    </tr>
<?php
}

}
