<?php

namespace Devmonsta\Options\Posts\Controls\RangeSlider;

use Devmonsta\Options\Posts\Structure;

class RangeSlider extends Structure {

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
    public function enqueue() {
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
        $content = $this->content;
        $from_val = $content['value']['from'];
        $to_val = $content['value']['to'];
        global $post;
        $this->value = (!is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                        && get_post_meta( $post->ID, $this->prefix . $content['name'], true ) !== "") ?
                            get_post_meta( $post->ID, $this->prefix . $content['name'], true )
                            : $from_val . "," . $to_val;
        
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
        $dynamic_classes = "";
        if ( is_array( $attrs ) && !empty( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {
                if($key == "class"){
                    $dynamic_classes .= $val . " ";
                }else{
                    $default_attributes .= $key . "='" . $val . "' ";
                }
               
            }

        }
        $class_attributes = "class='dm-option $dynamic_classes'";
        $default_attributes .= $class_attributes;

        ?>
        <div <?php echo dm_render_markup($default_attributes);?> >
            <label><?php echo esc_html( $label ); ?> </label>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <input class="dm-range-slider"
                    type="text" value="<?php echo esc_attr( $this->value ); ?>"
                    name="<?php echo esc_attr( $this->prefix . $name ); ?>"/>
        </div<>
    <?php
}

}
