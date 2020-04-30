<?php

namespace Devmonsta\Options\Posts\Controls\TypographyV2;

use Devmonsta\Options\Posts\Structure;

class TypographyV2 extends Structure {

    protected $value;
    /**
     * @internal
     */
    public function init() 
    {

    }
    /**
     * Return the list of Google Fonts from our json file. Unless otherwise specfied, list will be limited to 30 fonts.
     */
    public function customizer_getGoogleFonts( $count = 30 ) {
            $transient = "_newseqo_customizer_google_fonts";
            $key = 'AIzaSyAP_KwRh1HX7tNAbLDSGhZ8K4tfu4MW4kA';
            if(get_transient($transient)==false){
               $google_api= 'https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity&key='.$key.'';
                  
               $request = wp_remote_get( DM_OPTIONS.'/posts/controls/typography-v2/google-fonts-popularity.json' );
              
               if( is_wp_error( $request ) ) {
                  return "";
               }
               $body = wp_remote_retrieve_body( $request );
               $content = json_decode( $body );
               set_transient( $transient, $content, 86000 );
            }else{
                $content =get_transient($transient); 
            }
            dm_print(array_slice( $content->items, 0, $count ));
            if( $count == 'all' ) {
                return $content->items;
            } else {
                return array_slice( $content->items, 0, $count );
            }
           }
    /**
     * @internal
     */
    public function enqueue() {
        // load scripts
        add_action( 'admin_enqueue_scripts', [$this, 'load_scripts'] );
        // load color picker
        $this->dm_enqueue_color_picker();
    }
    /**
     * 
     *
     * @param [type] $hook
     * @return void
     */
    public function load_scripts($hook)
    {
        // js 
        wp_enqueue_script( 'dm-select2', DM_OPTIONS . 'posts/controls/typography-v2/assets/js/select2.min.js', ['jquery'], true );
        //css
        wp_enqueue_style( 'dm-slide-ranger-css', plugins_url( 'typography-v2/assets/css/ranger-slider.css', dirname( __FILE__ ) ) );
        wp_enqueue_style( 'dm-select2', plugins_url( 'typography-v2/assets/css/select2.min.css', dirname( __FILE__ ) ) );
    }

    /**
     * Return the list of Google Fonts from our json file. Unless otherwise specfied, list will be limited to 30 fonts.
     */
    public function dm_getGoogleFonts() {
        // Google Font Defaults
        $google_faces = array(
    
            'Arvo, serif' => 'Arvo',
    
            'Copse, sans-serif' => 'Copse',
    
            'Droid Sans, sans-serif' => 'Droid Sans',
    
            'Droid Serif, serif' => 'Droid Serif',
    
            'Lobster, cursive' => 'Lobster',
    
            'Nobile, sans-serif' => 'Nobile',
    
            'Open Sans, sans-serif' => 'Open Sans',
    
            'Oswald, sans-serif' => 'Oswald',
    
            'Pacifico, cursive' => 'Pacifico',
    
            'Rokkitt, serif' => 'Rokkit',
    
            'PT Sans, sans-serif' => 'PT Sans',
    
            'Quattrocento, serif' => 'Quattrocento',

            'Raleway, cursive' => 'Raleway',
    
            'Ubuntu, sans-serif' => 'Ubuntu',
    
            'Yanone Kaffeesatz, sans-serif' => 'Yanone Kaffeesatz'
    
        );
        return $google_faces;               
    }
    /**
     * @internal
     */
    function dm_enqueue_color_picker() {
        // for color
        if ( !wp_style_is( 'wp-color-picker', 'enqueued' ) ) {
            wp_enqueue_style( 'wp-color-picker' );
        }

        wp_enqueue_script( 'dm-typo-script-handle', DM_CORE . 'options/posts/controls/typography-v2/assets/js/scripts.js', ['jquery', 'wp-color-picker'], false, true );
        $google_fonts = $this->dm_getGoogleFonts();
        global $post;
        $data            = [];
        $data['default'] = ( !is_null( get_post_meta( $post->ID, $this->prefix . 'typograhy_color' , true ) ) ) 
                            ? get_post_meta( $post->ID, $this->prefix . 'typograhy_color' , true )
                            : $this->content['value']['color'];
        $data['google_fonts'] =  $google_fonts;  
        $data['google_selected_style'] =  $this->value['style'];                  
        wp_localize_script( 'dm-typo-script-handle', 'typo_config', $data );
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

        // font family 
        $typo_graphy['family'] = !is_null( get_post_meta( $post->ID, $this->prefix . "typograhy_family", true ) ) ?
        get_post_meta( $post->ID, $this->prefix . "typograhy_family", true )
        : $content['value']['family'];
        $typo_graphy['family'] == '' ? $typo_graphy['family'] = $content['value']['family'] : $typo_graphy['family'];

        // font style 
        $typo_graphy['style'] = !is_null( get_post_meta( $post->ID, $this->prefix . "typograhy_style", true ) ) ?
        get_post_meta( $post->ID, $this->prefix . "typograhy_style", true )
        : $content['value']['style'];
        $typo_graphy['style'] == '' ? $typo_graphy['style'] = $content['value']['style'] : $typo_graphy['style'];

        // font weight 
        $typo_graphy['weight'] = !is_null( get_post_meta( $post->ID, $this->prefix . "typograhy_weight", true ) ) ?
        get_post_meta( $post->ID, $this->prefix . "typograhy_weight", true )
        : $content['value']['weight'];
        $typo_graphy['weight'] == '' ? $typo_graphy['weight'] = $content['value']['weight'] : $typo_graphy['weight'];

         // font size 
        $typo_graphy['size'] = !is_null( get_post_meta( $post->ID, $this->prefix . "typograhy_size", true ) ) ?
        get_post_meta( $post->ID, $this->prefix . "typograhy_size", true )
        : $content['value']['size'];
        $typo_graphy['size'] == '' ? $typo_graphy['size'] = $content['value']['size'] : $typo_graphy['size'];
        
        // line-height
        $typo_graphy['line-height'] = !is_null( get_post_meta( $post->ID, $this->prefix . "typograhy_line_height", true ) ) ?
        get_post_meta( $post->ID, $this->prefix . "typograhy_line_height", true )
        : $content['value']['line-height'];
        $typo_graphy['line-height'] == '' ? $typo_graphy['line-height'] = $content['value']['line-height'] : $typo_graphy['line-height'];

         // letter-spacing
         $typo_graphy['letter-spacing'] = !is_null( get_post_meta( $post->ID, $this->prefix . "typograhy_letter_spacing", true ) ) ?
         get_post_meta( $post->ID, $this->prefix . "typograhy_letter_spacing", true )
         : $content['value']['letter-spacing'];
         $typo_graphy['letter-spacing'] == '' ? $typo_graphy['letter-spacing'] = $content['value']['letter-spacing'] : $typo_graphy['letter-spacing'];

        $this->value = $typo_graphy;
        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $this->customizer_getGoogleFonts();
        $font_list     = $this->dm_getGoogleFonts();
        $label        = isset( $this->content['label'] ) ? $this->content['label'] : '';
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
                        if( count($font_list) > 0 ) {
                            ?>
                            <div class="google_fonts_select_control">
                                <div class="google-fonts">
                                    <select class="google-fonts-list" name="<?php echo esc_attr($this->prefix . "typograhy_family") ?>">
                                        <?php
                                            foreach( $font_list as $key => $item ) {
                                                    $selected = $key == esc_html($value['family']) ? 'selected' : '';
                                                    echo '<option value="' . $key . '" '. $selected . '>' . $item . '</option>';
                                                }
                                        ?>
                                    </select>
                                </div>
                                <div class="style-weight">
                                    <select class="google-styel-list">

                                    </select>
                                </div>
                            </div>
                            <?php
                        }
                        break;
                    case 'size':
                        echo "<div>".esc_html( 'Size :')."</div>";
                        ?>
                        <input class="range-slider-font-size dm_group_typhography_range_font_size" type="range" min="0" max="100"   value="<?php echo (float)$value['size']; ?>">
                        <input type="text" name="<?php echo esc_attr($this->prefix . "typograhy_size") ?>" value=" <?php echo esc_html(trim( $value['size'] )) ; ?> "  id="size_value" /> 
                        <?php
                        break;
                    case 'line-height':
                        echo "<div>".esc_html( 'Line height:')."</div>";
                        ?>                        
                        <input class="range-slider-line-height dm_group_typhography_line_height" type="range" min="0" max="100"  value="<?php echo (float)$this->value["line-height"]; ?>">
                        <input type="text" name="<?php echo esc_attr($this->prefix . "typograhy_line_height") ?>" value=" <?php echo esc_html(trim($value["line-height"])); ?> "  id="line_height_value" /> 
                        <?php
                        break;
                    case 'letter-spacing':
                        echo "<div>".esc_html( 'Later space:')."</div>";
                        ?>
                           <input class="range-slide-letter-space dm_group_typhography_letterspace" type="range" min="-10" max="10"  value="<?php echo (float)$this->value["letter-spacing"]; ?>">
                           <input type="text" name="<?php echo esc_attr($this->prefix . "typograhy_letter_spacing") ?>" value="<?php echo esc_html(trim( $value["letter-spacing"]) ); ?>" id="latter_spacing_value" /> 
                        <?php
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
