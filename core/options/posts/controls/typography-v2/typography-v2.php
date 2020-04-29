<?php

namespace Devmonsta\Options\Posts\Controls\TypographyV2;

use Devmonsta\Options\Posts\Structure;

class TypographyV2 extends Structure {

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
        $this->dm_enqueue_color_picker();
    }

    /**
     * @internal
     */
    function dm_enqueue_color_picker() {
        // for ranger
          //css
          wp_enqueue_style( 'dm-slide-ranger-css', plugins_url( 'typography-v2/assets/css/ranger-slider.css', dirname( __FILE__ ) ) );
         // js 
         wp_enqueue_script( 'dm-slide-ranger-js', plugins_url( 'typography-v2/assets/js/ranger-slider.js', dirname( __FILE__ ) ), ['jquery'], true );

        // for color
        if ( !wp_style_is( 'wp-color-picker', 'enqueued' ) ) {
            wp_enqueue_style( 'wp-color-picker' );
        }

        wp_enqueue_script( 'dm-script-handle', DM_CORE . 'options/posts/controls/typography-v2/assets/js/color-script.js', ['jquery', 'wp-color-picker'], false, true );

        global $post;
        $data            = [];
        $data['default'] = ( !is_null( get_post_meta( $post->ID, $this->prefix . $this->content['name'], true ) ) ) 
                            ? get_post_meta( $post->ID, $this->prefix . $this->content['name'], true )
                            : $this->content['value']['color'];
        wp_localize_script( 'dm-script-handle', 'color_picker_config', $data );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        $typo_graphy = [];
        global $post;
        // color
        $typo_graphy['color'] = !is_null( get_post_meta( $post->ID, $this->prefix . "typograhy_color", true ) ) ?
        get_post_meta( $post->ID, $this->prefix . "typograhy_color", true )
        : $content['value']['color'];
        $typo_graphy['color'] == '' ? $typo_graphy['color'] = $content['value']['color'] : $typo_graphy['color'];
        $this->value = $typo_graphy;
        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label        = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name         = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc         = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $value        = isset( $this->content['value'] ) ? $this->content['value'] : [];
        $components   = isset( $this->content['components'] ) ? $this->content['components'] : [];
        $attrs = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $default_attributes = "";

        if ( is_array( $attrs ) && !empty( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {
                $default_attributes .= $key . "='" . $val . "' ";
            }

        }
        echo "<div ".esc_attr($default_attributes).">";
        echo "<div>".esc_html( $label )."</div>";
        echo "<div><small>".esc_html( $desc )."</small></div>";
        $value = $this->value;
        foreach ($components as $key => $item) {
            if ($key) {
                switch ($key) {
                    case 'family':
                        echo "<div>".esc_html( 'Family:')."</div>";
                        break;
                    case 'size':
                        echo "<div>".esc_html( 'Size :')."</div>";
                        ?>
                        <input class="range-slider dm_group_typhography_range_font_size" type="range" min="0" max="100"  value="<?php echo (float)$this->fontValues["font-size"]; ?>">
                        <input type="text" value=" <?php echo esc_html(trim($this->fontValues["font-size"])) ; ?> " class="google-fonts-size-style" /> 
                        <?php
                        break;
                    case 'line-height':
                        echo "<div>".esc_html( 'Line height:')."</div>";
    
                        break;
                    case 'letter-spacing':
                        echo "<div>".esc_html( 'Later space:')."</div>";
    
                        break;
                    case 'color':
                        echo "<div>".esc_html( 'Color:')."</div>";
                        ?>
                        <input  type="text"
                        name="<?php echo esc_attr( $this->prefix . 'typograhy_color' ); ?>"
                        value="<?php echo esc_attr( $value['color'] ); ?>"
                        class="dm-typography-color-field"
                        data-default-color="<?php echo esc_attr( $value['color'] ); ?>" />
                        <?php
                        break;
                    
                    default:
                        # code...
                        break;
                }
            }
        }
        echo "</div>";
    }

}
