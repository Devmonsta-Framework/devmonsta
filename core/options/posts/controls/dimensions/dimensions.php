<?php

namespace Devmonsta\Options\Posts\Controls\Dimensions;

use Devmonsta\Options\Posts\Structure;

class Dimensions extends Structure {

    /**
     * @internal
     */
    public function init() {
    }

    public function enqueue() {
        add_action( 'init', [$this, 'enqueue_dimensions_scripts'] );
    }

    public function enqueue_dimensions_scripts() {
        wp_enqueue_style( 'dm-dimensions-css', DM_CORE . 'options/posts/controls/dimensions/assets/css/style.css', [], time(), true );
        wp_enqueue_script( 'dm-dimensions', DM_CORE . 'options/posts/controls/dimensions/assets/js/script.js', ['jquery'], time(), true );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;
        $this->value = ( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) !== "" &&
                        !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) ?
                        maybe_unserialize(get_post_meta( $post->ID, $this->prefix . $content['name'], true ))  
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
            <div class="dm-dimensions-input">
                <label>top</label><input class="dm-dimension-number-input input-top" style="width:50px; border: '1px solid slategray'" type="number" name="<?php echo esc_attr( $this->prefix . $name ); ?>[top]" value="<?php echo isset($this->value["top"]) ? esc_html( intval($this->value["top"]) ) : 0; ?>" min="0"/>
                <label>right</label><input class="dm-dimension-number-input input-right"  style="width:50px; border: '1px solid slategray'" type="number" name="<?php echo esc_attr( $this->prefix . $name ); ?>[right]" value="<?php echo isset($this->value["right"]) ? esc_html( intval($this->value["right"]) ) : 0; ?>"  min="0"/>
                <label>bottom</label><input class="dm-dimension-number-input input-bottom" style="width:50px; border: '1px solid slategray'" type="number" name="<?php echo esc_attr( $this->prefix . $name ); ?>[bottom]" value="<?php echo isset($this->value["bottom"]) ? esc_html( intval($this->value["bottom"]) ) : 0; ?>"  min="0"/>
                <label>left</label><input class="dm-dimension-number-input input-left" style="width:50px; border: '1px solid slategray'" type="number" name="<?php echo esc_attr( $this->prefix . $name ); ?>[left]" value="<?php echo isset($this->value["left"]) ? esc_html( intval($this->value["left"]) ) : 0; ?>"  min="0"/>
                
                <input class="dm-dimension-linked-input" type="hidden" name="<?php echo esc_attr($this->prefix . $name);?>[isLinked]" value="<?php echo isset($this->value["isLinked"]) ? esc_html( intval($this->value["isLinked"]) ) : 0;?>"/>
                <button class="dm-dimension-attachment-input <?php echo intval($this->value["isLinked"]) == 1 ? 'clicked' : ''; ?>" style="cursor:pointer; width:50px; height: 30px; border: 1px solid gray;"></button>
            </div>
        </div>
        
    <?php
}

}
