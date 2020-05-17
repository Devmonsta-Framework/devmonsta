<?php

namespace Devmonsta\Options\Posts\Controls\RangeSlider;

use Devmonsta\Options\Posts\Structure;

class RangeSlider extends Structure {

    protected $current_screen;
    protected $min_val;
    protected $max_val;

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
        ( $this->current_screen == "post" ) ? $this->enqueue_slider_scripts() : add_action( 'init', [$this, 'enqueue_slider_scripts'] );

    }

    public function enqueue_slider_scripts() {
        wp_enqueue_style( 'asRange-css', DM_CORE . 'options/posts/controls/range-slider/assets/css/asRange.css' );
        wp_enqueue_script( 'asRange-js', DM_CORE . 'options/posts/controls/range-slider/assets/js/jquery-asRange.js' );
        wp_enqueue_script( 'dm-range-slider', DM_CORE . 'options/posts/controls/range-slider/assets/js/script.js', ['jquery', 'asRange-js'], time(), true );

        $range_slider_config       = $this->content['properties'];
        $range_slider_data['min']  = isset( $range_slider_config['min'] ) ? $range_slider_config['min'] : 0;
        $range_slider_data['max']  = isset( $range_slider_config['max'] ) ? $range_slider_config['max'] : 100;
        $range_slider_data['step'] = isset( $range_slider_config['step'] ) ? $range_slider_config['step'] : 1;

        wp_localize_script( 'dm-range-slider', 'range_slider_config', $range_slider_data );

    }

    /**
     * @internal
     */
    public function render() {
        $content  = $this->content;
        $from_val = $content['value']['from'];
        $to_val   = $content['value']['to'];
        global $post;
        $this->value = (  ( $this->current_screen == "post" ) && ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
            && ( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) !== "" ) ) ?
        get_post_meta( $post->ID, $this->prefix . $content['name'], true )
        : $from_val . "," . $to_val;

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label  = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name   = isset( $this->content['name'] ) ? $this->prefix  . $this->content['name'] : '';
        $desc   = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $desc   = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs  = isset( $this->content['attr'] ) ? $this->content['attr'] : '';

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
                <input class="dm-range-slider"
                    type="text" value="<?php echo esc_attr( $this->value ); ?>"
                    name="<?php echo esc_attr( $name ); ?>"/>
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
                    echo esc_html( get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) ? get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) : "" );
                }

                return $content;

            }, 10, 3 );
    }

    public function edit_fields( $term, $taxonomy ) {

        $this->enqueue_slider_scripts();

        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix  . $this->content['name'] : '';
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
        <input class="dm-range-slider"
                    type="text" value="<?php echo esc_attr( $value ); ?>"
                    name="<?php echo esc_attr( $name ); ?>"/>

            <br><small class="dm-option-desc">(<?php echo esc_html( $desc ); ?> )</small>
        </td>
    </tr>
<?php
}

}
