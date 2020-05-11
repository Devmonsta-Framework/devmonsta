<?php

namespace Devmonsta\Options\Posts\Controls\Typography;

use Devmonsta\Options\Posts\Structure;

class Typography extends Structure {

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

        if ( $this->current_screen == "post" ) {
            add_action( 'init', [$this, 'dm_enqueue_color_picker'] );
        } elseif ( $this->current_screen == "taxonomy" ) {
            $this->dm_enqueue_color_picker();
        }

    }

    /**
     * @internal
     */
    function dm_enqueue_color_picker() {
        //css
        wp_enqueue_style( 'dm-slide-ranger-css', plugins_url( 'typography/assets/css/ranger-slider.css', dirname( __FILE__ ) ) );

        if ( !wp_style_is( 'wp-color-picker', 'enqueued' ) ) {
            wp_enqueue_style( 'wp-color-picker' );
        }

        wp_enqueue_script( 'dm-typo-script-handle', DM_CORE . 'options/posts/controls/typography/assets/js/scripts.js', ['jquery', 'wp-color-picker'], false, true );
    }

    /**
     * Return the list of Google Fonts from our json file. Unless otherwise specfied, list will be limited to 30 fonts.
     */
    public function dm_getGoogleFonts( $count = 30 ) {
        $transient = "_newseqo_customizer_google_fonts";

        if ( get_transient( $transient ) == false ) {
            $request = wp_remote_get( DM_OPTIONS . '/posts/controls/typography/google-fonts-popularity.json' );

            if ( is_wp_error( $request ) ) {
                return "";
            }

            $body    = wp_remote_retrieve_body( $request );
            $content = json_decode( $body );
            set_transient( $transient, $content, 86000 );
        } else {
            $content = get_transient( $transient );
        }

        if ( $count == 'all' ) {
            return $content->items;
        } else {
            return array_slice( $content->items, 0, $count );
        }

    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;

        $typo_graphy = [];
        global $post;

        // color
        $typo_graphy['color'] = ( $this->current_screen == "post" ) && !is_null( get_post_meta( $post->ID, $this->prefix . "typograhy_color", true ) ) ?
        get_post_meta( $post->ID, $this->prefix . "typograhy_color", true )
        : $content['value']['color'];

        $typo_graphy['color'] == '' ? $typo_graphy['color'] = $content['value']['color'] : $typo_graphy['color'];

        // font family
        $typo_graphy['family'] = ( $this->current_screen == "post" ) && !is_null( get_post_meta( $post->ID, $this->prefix . "typograhy_family", true ) ) ?
        get_post_meta( $post->ID, $this->prefix . "typograhy_family", true )
        : $content['value']['family'];
        $typo_graphy['family'] == '' ? $typo_graphy['family'] = $content['value']['family'] : $typo_graphy['family'];

        // font style
        $typo_graphy['style'] = ( $this->current_screen == "post" ) && !is_null( get_post_meta( $post->ID, $this->prefix . "typograhy_style", true ) ) ?
        get_post_meta( $post->ID, $this->prefix . "typograhy_style", true )
        : $content['value']['style'];
        $typo_graphy['style'] == '' ? $typo_graphy['style'] = $content['value']['style'] : $typo_graphy['style'];

        // font weight
        $typo_graphy['weight'] = ( $this->current_screen == "post" ) && !is_null( get_post_meta( $post->ID, $this->prefix . "typograhy_weight", true ) ) ?
        get_post_meta( $post->ID, $this->prefix . "typograhy_weight", true )
        : $content['value']['weight'];
        $typo_graphy['weight'] == '' ? $typo_graphy['weight'] = $content['value']['weight'] : $typo_graphy['weight'];

        // font size
        $typo_graphy['size'] = ( $this->current_screen == "post" ) && !is_null( get_post_meta( $post->ID, $this->prefix . "typograhy_size", true ) ) ?
        get_post_meta( $post->ID, $this->prefix . "typograhy_size", true )
        : $content['value']['size'];
        $typo_graphy['size'] == '' ? $typo_graphy['size'] = $content['value']['size'] : $typo_graphy['size'];

        // line-height
        $typo_graphy['line-height'] = ( $this->current_screen == "post" ) && !is_null( get_post_meta( $post->ID, $this->prefix . "typograhy_line_height", true ) ) ?
        get_post_meta( $post->ID, $this->prefix . "typograhy_line_height", true )
        : $content['value']['line-height'];
        $typo_graphy['line-height'] == '' ? $typo_graphy['line-height'] = $content['value']['line-height'] : $typo_graphy['line-height'];

        // letter-spacing
        $typo_graphy['letter-spacing'] = ( $this->current_screen == "post" ) && !is_null( get_post_meta( $post->ID, $this->prefix . "typograhy_letter_spacing", true ) ) ?
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
        $font_list             = $this->dm_getGoogleFonts();
        $data['font_list']     = $font_list;
        $data['selected_data'] = $this->value;
        wp_localize_script( 'dm-typo-script-handle', 'typo_config', $data );

        $label      = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $desc       = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $value      = isset( $this->content['value'] ) ? $this->content['value'] : [];
        $components = isset( $this->content['components'] ) ? $this->content['components'] : [];

        echo "<div>" . esc_html( $label ) . "</div>";
        echo "<div><small class='dm-option-desc'>" . esc_html( $desc ) . "</small></div>";
        $value = $this->value;

        foreach ( $components as $key => $item ) {

            if ( $key ) {

                switch ( $key ) {
                case 'family':
                    echo "<div>" . esc_html( 'Family:' ) . "</div>";

                    if ( count( $font_list ) > 0 ) {
                        ?>
                        <div class="google_fonts_select_control">
                            <div class="google-fonts">
                                <select class="google-fonts-list" name="<?php echo esc_attr( $this->prefix . "typograhy_family" ) ?>">
                                <?php
                                    foreach ( $font_list as $key => $item ) {
                                        $selected = $item->family == esc_html( $value['family'] ) ? 'selected' : '';
                                        echo '<option value="' . $item->family . '" ' . $selected . '>' . $item->family . '</option>';
                                    }
                                ?>
                                </select>
                            </div>
                            <div class="weight">
                                <label class="dm-option-label">Weight</label>
                                <select name="<?php echo esc_attr( $this->prefix . "typograhy_weight" ) ?>" class="google-weight-list">
                                </select>
                            </div>
                            <div class="style">
                                <label class="dm-option-label">Style</label>
                                <select name="<?php echo esc_attr( $this->prefix . "typograhy_style" ) ?>" class="google-style-list">
                                </select>
                            </div>
                        </div>
                    <?php
                    }

                    break;


                case 'size':
                    echo "<div>" . esc_html( 'Size :' ) . "</div>";
                    ?>
                    <input class="range-slider-font-size dm_group_typhography_range_font_size" type="range" min="0" max="100"
                        value="<?php echo (float) $value['size']; ?>">
                    <input type="text" name="<?php echo esc_attr( $this->prefix . "typograhy_size" ) ?>"
                        value=" <?php echo esc_html( trim( $value['size'] ) ); ?> " id="size_value" />
                    <?php
                    break;

                case 'line-height':
                    echo "<div>" . esc_html( 'Line height:' ) . "</div>";
                    ?>
                    <input class="range-slider-line-height dm_group_typhography_line_height" type="range" min="0" max="100"
                        value="<?php echo (float) $this->value["line-height"]; ?>">
                    <input type="text" name="<?php echo esc_attr( $this->prefix . "typograhy_line_height" ) ?>"
                        value=" <?php echo esc_html( trim( $value["line-height"] ) ); ?> " id="line_height_value" />
                    <?php
                    break;

                case 'letter-spacing':
                    echo "<div>" . esc_html( 'Later space:' ) . "</div>";
                    ?>
                    <input class="range-slide-letter-space dm_group_typhography_letterspace" type="range" min="-10" max="10"
                        value="<?php echo (float) $this->value["letter-spacing"]; ?>">
                    <input type="text" name="<?php echo esc_attr( $this->prefix . "typograhy_letter_spacing" ) ?>"
                        value="<?php echo esc_html( trim( $value["letter-spacing"] ) ); ?>" id="latter_spacing_value" />
                    <?php
                    break;
                
                case 'color':
                    echo "<div>" . esc_html( 'Color:' ) . "</div>";
                    ?>
                    <input type="text" name="<?php echo esc_attr( $this->prefix . 'typograhy_color' ); ?>"
                        value="<?php echo esc_attr( $value['color'] ); ?>" class="dm-typography-color-field"
                        data-default-color="<?php echo esc_attr( $value['color'] ); ?>" />
                    <?php
                    break;

                default:
                    break;
                }

            }

        }

    }

    public function columns() {

    }

    public function edit_fields( $term, $taxonomy ) {
        $this->load_switcher_scripts();

        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $prefix             = 'devmonsta_';
        $name               = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $left_choice        = isset( $this->content['left-choice'] ) ? $this->content['left-choice'] : '';
        $right_choice       = isset( $this->content['right-choice'] ) ? $this->content['right-choice'] : '';
        $left_key           = array_key_first( $left_choice );
        $right_key          = array_key_first( $right_choice );
        $value              = get_term_meta( $term->term_id, $name, true );
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

        $class_attributes = "class='dm-option term-group-wrap $dynamic_classes'";
        $default_attributes .= $class_attributes;

        // add inline css for dynamic value
        $style = '';
        $style .= '
        .dm_switcher_item label.dm_switcher_label:before {
            content: "' . esc_attr( $left_choice[$left_key] ) . '";
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #fff;
            font-size: 10px;
            display: inline-block;
            text-transform: uppercase;
            font-weight: 600;
        }
        .dm_switcher_item input.dm-control-input:checked + label.dm_switcher_label:before {
            content: "' . esc_attr( $right_choice[$right_key] ) . '";
            right: inherit;
            left: 10px;
        }
        ';
        wp_register_style( 'dm-switcher-inline-css', false, ['dm-switcher'] );
        wp_enqueue_style( 'dm-switcher-inline-css' );
        wp_add_inline_style( 'dm-switcher-inline-css', $style );

        ?>

<tr <?php echo dm_render_markup( $default_attributes ); ?>>
    <th scope="row">
        <label class="dm-option-label"><?php echo esc_html( $label ); ?></label>
    </th>
    <td>
        <div class='dm_switcher_main_block'>
            <div class='dm_switcher_item'>
                <input id='dm_switcher_right' type='checkbox' value='<?php echo esc_attr( $right_key ); ?>'
                    class='dm-control-input' name='<?php echo esc_attr( $name ); ?>'
                    <?php echo ( $value == $right_key ) ? 'checked' : ''; ?> />
                <label class='dm_switcher_label dm-option-label'></label>
            </div>
            <input id='dm_switcher_left' type='checkbox' value='<?php echo esc_attr( $left_key ); ?>' class=''
                name='<?php echo esc_attr( $name ); ?>' <?php echo ( $value == $left_key ) ? 'checked' : ''; ?> />
        </div>
        <br><small class="dm-option-desc">(<?php echo esc_html( $desc ); ?> )</small>
    </td>
</tr>
<?php
}

}
