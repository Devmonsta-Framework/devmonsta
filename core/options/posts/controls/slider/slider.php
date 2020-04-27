<?php

namespace Devmonsta\Options\Posts\Controls\Slider;

use Devmonsta\Options\Posts\Structure;

class Slider extends Structure {

    protected $value;

    /**
     * @internal
     */
    public function init() {

    }

    /**
     * @internal
     */
    public function enqueue() {
        add_action( 'init', [$this, 'enqueue_all_scripts'] );
    }

    /**
     * @internal
     */
    public function enqueue_all_scripts() {
        wp_enqueue_style( 'dm-slider-asrange-css', DM_CORE . 'options/posts/controls/slider/assets/css/asRange.css' );
        wp_enqueue_script( 'dm-slider-asrange', DM_CORE . 'options/posts/controls/slider/assets/js/jquery-asRange.min.js' );
        wp_enqueue_script( 'dm-slider-script', DM_CORE . 'options/posts/controls/slider/assets/js/script.js', ['jquery', 'dm-slider-asrange', 'dm-slider-asrange-css'], time(), true );
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

        $this->value = ( "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true )
            && !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) ?
        get_post_meta( $post->ID, $this->prefix . $content['name'], true )
        : $content['value'];
        $this->output();
    }

    /**
     * @internal
     */
    public function output() {

        $lable   = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name    = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc    = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $default_attributes = "";

        if ( is_array( $attrs ) && !empty( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {
                $default_attributes .= $key . "='" . $val . "' ";
            }

        }

        ?>

        <div <?php echo esc_attr($default_attributes);?>>
            <lable><?php echo esc_html( $lable ); ?> </lable>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <input class="dm-slider" 
            type="range" min="0" max="10" step="0.01" 
            name="<?php echo esc_attr( $this->prefix . $name ); ?>" 
            value="<?php echo esc_attr( $this->value ); ?>"/>
        </div>
        
    <?php
}

}
