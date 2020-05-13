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

        if (  ( $this->current_screen == "post" ) || ( $this->current_screen == "taxonomy" ) ) {
            add_action( 'admin_enqueue_scripts', [$this, 'load_scripts'] );
        }

    }

    /**
     *
     *
     * @param [type] $hook
     * @return void
     */
    public function load_scripts() {
        $this->dm_enqueue_color_picker();
        wp_enqueue_style( 'select2-css', plugins_url( 'select/assets/css/select2.min.css', dirname( __FILE__ ) ) );
        wp_enqueue_script( 'select2-js', plugins_url( 'select/assets/js/select2.min.js', dirname( __FILE__ ) ) );
        wp_enqueue_style( 'dm-slide-ranger-css', plugins_url( 'typography/assets/css/ranger-slider.css', dirname( __FILE__ ) ) );
    }

    /**
     * @internal
     */
    function dm_enqueue_color_picker() {

        if ( !wp_style_is( 'wp-color-picker', 'enqueued' ) ) {
            wp_enqueue_style( 'wp-color-picker' );
        }

        wp_enqueue_script( 'dm-typo-script-handle', DM_CORE . 'options/posts/controls/typography/assets/js/scripts.js', ['jquery', 'wp-color-picker'], false, true );

    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;

        $this->value = (  ( $this->current_screen == "post" )
            && !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
            && "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
        ? maybe_unserialize( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
        : $content['value'];
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
        $name  = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : "";
        $label = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $desc  = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        // $value      = isset( $this->content['value'] ) ? $this->content['value'] : [];
        $components = isset( $this->content['components'] ) ? $this->content['components'] : [];

        echo "<div>" . esc_html( $label ) . "</div>";
        echo "<div><small>" . esc_html( $desc ) . "</small></div>";

        foreach ( $components as $key => $item ) {

            if ( $key ) {

                switch ( $key ) {
                case 'family':
                    echo "<div>" . esc_html( 'Family:' ) . "</div>";

                    if ( count( $font_list ) > 0 ) {
                        ?>
                            <div class="google_fonts_select_control">
                                <div class="google-fonts">
                                    <select class="google-fonts-list" name="<?php echo esc_attr( $name ) ?>[family]">
                                        <?php

                        foreach ( $font_list as $key => $item ) {
                            $selected = $item->family == esc_html( $this->value["family"] ) ? 'selected' : '';
                            echo '<option value="' . $item->family . '" ' . $selected . '>' . $item->family . '</option>';
                        }

                        ?>
                                    </select>
                                </div>
                                <div class="weight">
                                    <label>Weight</label>
                                    <select name="<?php echo esc_attr( $name ) ?>[weight]" class="google-weight-list">
                                    </select>
                                </div>
                                <div class="style">
                                    <label>Style</label>
                                    <select name="<?php echo esc_attr( $name ) ?>[style]" class="google-style-list">
                                    </select>
                                </div>
                            </div>
                            <?php
}

                    break;
                case 'size':
                    echo "<div>" . esc_html( 'Size :' ) . "</div>";
                    ?>
                        <input class="range-slider-font-size dm_group_typhography_range_font_size" type="range" min="0" max="100"   value="<?php echo isset( $this->value["size"] ) ? esc_html( floatval( $this->value["size"] ) ) : 0.00; ?>">
                        <input type="text" name="<?php echo esc_attr( $name ) ?>[size]"
                        value="<?php echo isset( $this->value["size"] ) ? esc_html( trim( $this->value["size"] ) ) : 0.00; ?>"  id="size_value" />
                        <?php
break;
                case 'line-height':
                    echo "<div>" . esc_html( 'Line height:' ) . "</div>";
                    ?>
                        <input class="range-slider-line-height dm_group_typhography_line_height" type="range" min="0" max="100"  value="<?php echo isset( $this->value["line_height"] ) ? esc_html( floatval( $this->value["line_height"] ) ) : 0.00; ?>">
                        <input type="text"name="<?php echo esc_attr( $name ) ?>[line_height]"
                        value="<?php echo isset( $this->value["line_height"] ) ? esc_html( trim( $this->value["line_height"] ) ) : 0.00; ?>"   id="line_height_value" />
                        <?php
break;
                case 'letter-spacing':
                    echo "<div>" . esc_html( 'Later space:' ) . "</div>";
                    ?>
                           <input class="range-slide-letter-space dm_group_typhography_letterspace" type="range" min="-10" max="10"   value="<?php echo isset( $this->value["letter_spacing"] ) ? esc_html( floatval( $this->value["letter_spacing"] ) ) : 0.00; ?>">
                           <input type="text" name="<?php echo esc_attr( $name ) ?>[letter_spacing]"
                        value="<?php echo isset( $this->value["letter_spacing"] ) ? esc_html( trim( $this->value["letter_spacing"] ) ) : 0.00; ?>" id="latter_spacing_value" />
                        <?php
break;
                case 'color':
                    echo "<div>" . esc_html( 'Color:' ) . "</div>";
                    ?>
                        <input  type="text"
                        name="<?php echo esc_attr( $name ) ?>[color]"
                        value="<?php echo isset( $this->value["color"] ) ? esc_html( $this->value["color"] ) : ""; ?>"
                        class="dm-typography-color-field"
                        data-default-color="<?php echo esc_attr( $this->value["color"] ); ?>" />
                        <?php
break;

                default:
                    # code...
                    break;
                }

            }

        }

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
                    print_r( esc_html( maybe_unserialize( get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) ) ) );

                }

                return $content;

            }, 10, 3 );
    }

    public function edit_fields( $term, $taxonomy ) {

        $name  = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : "";
        $value = maybe_unserialize( get_term_meta( $term->term_id, $name, true ) );

        $this->load_scripts();

        $font_list             = $this->dm_getGoogleFonts();
        $data['font_list']     = $font_list;
        $data['selected_data'] = $value;
        wp_localize_script( 'dm-typo-script-handle', 'typo_config', $data );

        $label      = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $desc       = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs      = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $components = isset( $this->content['components'] ) ? $this->content['components'] : [];

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

        <!-- typography markup starts -->
        <?php

        foreach ( $components as $key => $item ) {

            if ( $key ) {

                switch ( $key ) {
                case 'family':
                    echo "<div>" . esc_html( 'Family:' ) . "</div>";

                    if ( count( $font_list ) > 0 ) {
                        ?>
                <div class="google_fonts_select_control">
                    <div class="google-fonts">
                        <select class="google-fonts-list" name="<?php echo esc_attr( $name ) ?>[family]">
                            <?php

                        foreach ( $font_list as $key => $item ) {
                            $selected = $item->family == esc_html( $value["family"] ) ? 'selected' : '';
                            echo '<option value="' . $item->family . '" ' . $selected . '>' . $item->family . '</option>';
                        }

                        ?>
                        </select>
                    </div>
                    <div class="weight">
                        <label>Weight</label>
                        <select name="<?php echo esc_attr( $name ) ?>[weight]" class="google-weight-list">
                        </select>
                    </div>
                    <div class="style">
                        <label>Style</label>
                        <select name="<?php echo esc_attr( $name ) ?>[style]" class="google-style-list">
                        </select>
                    </div>
                </div>
                <?php
}

                    break;
                case 'size':
                    echo "<div>" . esc_html( 'Size :' ) . "</div>";
                    ?>
            <input class="range-slider-font-size dm_group_typhography_range_font_size" type="range" min="0" max="100"   value="<?php echo isset( $value["size"] ) ? esc_html( floatval( $value["size"] ) ) : 0.00; ?>">
            <input type="text" name="<?php echo esc_attr( $name ) ?>[size]"
            value="<?php echo isset( $value["size"] ) ? esc_html( trim( $value["size"] ) ) : 0.00; ?>"  id="size_value" />
            <?php
break;
                case 'line-height':
                    echo "<div>" . esc_html( 'Line height:' ) . "</div>";
                    ?>
            <input class="range-slider-line-height dm_group_typhography_line_height" type="range" min="0" max="100"  value="<?php echo isset( $value["line_height"] ) ? esc_html( floatval( $value["line_height"] ) ) : 0.00; ?>">
            <input type="text"name="<?php echo esc_attr( $name ) ?>[line_height]"
            value="<?php echo isset( $value["line_height"] ) ? esc_html( trim( $value["line_height"] ) ) : 0.00; ?>"   id="line_height_value" />
            <?php
break;
                case 'letter-spacing':
                    echo "<div>" . esc_html( 'Later space:' ) . "</div>";
                    ?>
               <input class="range-slide-letter-space dm_group_typhography_letterspace" type="range" min="-10" max="10"   value="<?php echo isset( $value["letter_spacing"] ) ? esc_html( floatval( $value["letter_spacing"] ) ) : 0.00; ?>">
               <input type="text" name="<?php echo esc_attr( $name ) ?>[letter_spacing]"
            value="<?php echo isset( $value["letter_spacing"] ) ? esc_html( trim( $value["letter_spacing"] ) ) : 0.00; ?>" id="latter_spacing_value" />
            <?php
break;
                case 'color':
                    echo "<div>" . esc_html( 'Color:' ) . "</div>";
                    ?>
            <input  type="text"
            name="<?php echo esc_attr( $name ) ?>[color]"
            value="<?php echo isset( $value["color"] ) ? esc_html( $value["color"] ) : ""; ?>"
            class="dm-typography-color-field"
            data-default-color="<?php echo esc_attr( $value["color"] ); ?>" />
            <?php
break;

                default:
                    # code...
                    break;
                }

            }

        }

        ?>
        <!-- typography markup ends -->


        <br><small class="dm-option-desc">(<?php echo esc_html( $desc ); ?> )</small>
    </td>
</tr>
<?php
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

}
