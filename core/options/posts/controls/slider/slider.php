<?php

namespace Devmonsta\Options\Posts\Controls\Slider;

use Devmonsta\Options\Posts\Structure;

class Slider extends Structure {

    protected $current_screen;
    protected $value;

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
        add_action( 'init', [$this, 'enqueue_slider_scripts'] );
    }

    /**
     * @internal
     */
    public function enqueue_slider_scripts() {
        wp_enqueue_style( 'dm-slider-asrange-css', DM_CORE . 'options/posts/controls/slider/assets/css/asRange.css' );
        wp_enqueue_script( 'dm-slider-asrange', DM_CORE . 'options/posts/controls/slider/assets/js/jquery-asRange.min.js' );
        wp_enqueue_script( 'dm-slider-script', DM_CORE . 'options/posts/controls/slider/assets/js/script.js', ['jquery', 'dm-slider-asrange'], time(), true );

        //get slider settings from theme
        $dm_slider_data_config  = $this->content['properties'];
        $dm_slider_data['min']  = isset( $dm_slider_data_config['min'] ) ? $dm_slider_data_config['min'] : 0;
        $dm_slider_data['max']  = isset( $dm_slider_data_config['max'] ) ? $dm_slider_data_config['max'] : 100;
        $dm_slider_data['step'] = isset( $dm_slider_data_config['step'] ) ? $dm_slider_data_config['step'] : 1;
        wp_localize_script( 'dm-slider-script', 'dm_slider_config', $dm_slider_data );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;

        global $post;

        $this->value = (  ( $this->current_screen == "post" )
            && ( "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
            && ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) ) ?
        get_post_meta( $post->ID, $this->prefix . $content['name'], true )
        : $content['value'];

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
                <input class="dm-slider"
                        type="range"
                        name="<?php echo esc_attr( $name ); ?>"
                        value="<?php echo esc_attr( $this->value ); ?>"/>
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
                    echo esc_html(  "" != get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) ? get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) : "" );
                }

                return $content;

            }, 10, 3 );
    }

    public function edit_fields( $term, $taxonomy ) {

        $this->enqueue_slider_scripts();

        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $value              = get_term_meta( $term->term_id, $name, true ) ;
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
                <input class="dm-slider"
                    type="range"
                    name="<?php echo esc_attr( $name ); ?>"
                    value="<?php echo esc_attr( $value ); ?>"/>

                <br><small class="dm-option-desc">(<?php echo esc_html( $desc ); ?> )</small>
            </td>
        </tr>
<?php
}

}
