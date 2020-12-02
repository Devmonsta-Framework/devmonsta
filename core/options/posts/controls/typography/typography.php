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
    }
    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;

        $default_value = isset( $content['value'] ) && is_array( $content['value'] ) ? $content['value'] : [];
        $this->value   = (  ( $this->current_screen == "post" )
                        && !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                        && "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                        ? maybe_unserialize( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                        : $default_value;
        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : "";
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $components         = isset( $this->content['components'] ) && is_array( $this->content['components'] ) ? $this->content['components'] : [];

        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $this->value, $desc, $components );
    }

    /**
     * @internal
     */
    public function columns() {
        $visible = false;
        $content = $this->content;
        add_filter( 'manage_edit-' . $this->taxonomy . '_columns',
            function ( $columns ) use ( $content, $visible ) {

                $visible = ( isset( $content['show_in_table'] ) && $content['show_in_table'] === true ) ? true : false;

                if ( $visible ) {
                    $columns[$content['name']] =esc_html__( $content['label'], 'devmonsta' );
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

    /**
     * @internal
     */
    public function edit_fields( $term, $taxonomy ) {
        $name  = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : "";
        $value = (  ( "" != get_term_meta( $term->term_id, $name, true ) ) && ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) ) ? maybe_unserialize( get_term_meta( $term->term_id, $name, true ) ) : [];

        $label      = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $desc       = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $components = isset( $this->content['components'] ) && is_array( $this->content['components'] ) ? $this->content['components'] : [];

        //generate attributes dynamically for parent tag
        $default_attributes = $this->prepare_default_attributes( $this->content );

        //generate markup for control
        $this->generate_markup( $default_attributes, $label, $name, $value, $desc, $components );
    }

    /**
     * Return the list of Google Fonts from our json file. Unless otherwise specfied, list will be limited to 30 fonts.
     */
    public function devm_getGoogleFonts( $count = 30 ) {
        $transient = "_devm_google_fonts";

        if ( get_transient( $transient ) == false ) {
            $request = wp_remote_get( DEVMONSTA_OPTIONS . '/posts/controls/typography/google-fonts-popularity.json' );

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
     * Renders markup with given attributes
     *
     * @param [type] $default_attributes
     * @param [type] $label
     * @param [type] $name
     * @param [type] $value
     * @param [type] $desc
     * @param [type] $components
     * @param [type] $font_list
     * @return void
     */
    public function generate_markup( $default_attributes, $label, $name, $value, $desc, $components ) {
        $font_list             = $this->devm_getGoogleFonts();
        $data                  = [];
        $data['font_list']     = $font_list;
        $data['selected_data'] = $value;
        // wp_localize_script( 'dm-typo-script-handle', 'typo_config', $data );
        ?>
        <div <?php echo devm_render_markup( $default_attributes ); ?> >
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $label ); ?></label>
            </div>
            <div class="devm-option-column right full-width">
                <ul class="devm-option-typography" data-config='<?php echo json_encode($data); ?>'>
                <?php
                if ( is_array( $components ) && !empty( $components ) ) {
                    foreach ( $components as $key => $item ) {
                        if ( $item ) {
                            switch ( $key ) {
                            case 'family':
                                ?>
                                <li>
                                <?php
                                if ( is_array( $font_list ) && count( $font_list ) > 0 ): ?>
                                    <div class="google-fonts">
                                        <select class="devm-ctrl google-fonts-list" name="<?php echo esc_attr( $name ) ?>[family]">
                                        <?php
                                        foreach ( $font_list as $key => $item ) {
                                            $selected = ( $item->family == esc_html( isset($value["family"] ))) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo esc_attr( $item->family ); ?>" <?php echo esc_attr( $selected ); ?> ><?php echo esc_html( $item->family ); ?></option>
                                            <?php
                                        }
                                        ?>
                                        </select>
                                    </div>
                                <?php endif;?>
                                    <label><?php echo esc_html_e( 'Family', 'devmonsta' ); ?></label>
                                </li>
                                <li>
                                    <select data-selected_value='<?php echo isset( $value["weight"] )  ? esc_attr( $value["weight"] ) : "" ;?>' name="<?php echo esc_attr( $name ) ?>[weight]" class="devm-option-input devm-ctrl google-weight-list"></select>
                                    <label><?php echo esc_html_e( 'Weight', 'devmonsta' ); ?></label>
                                </li>
                                <li>
                                    <select data-selected_value='<?php echo isset( $value["weight"] ) ? esc_attr( $value["style"] ) : "" ;?>' name="<?php echo esc_attr( $name ) ?>[style]" class="devm-option-input devm-ctrl google-style-list"></select>
                                    <label><?php echo esc_html_e( 'Style', 'devmonsta' ); ?></label>
                                </li>
                                <?php
                            break;
                            case 'size':
                                ?>
                                <li>
                                    <input type="number" name="<?php echo esc_attr( $name ) ?>[size]" value="<?php echo isset( $value["size"] ) && is_numeric( $value["size"] ) ? esc_html( trim( $value["size"] ) ) : 0.00; ?>"  id="size_value" class="devm-option-input devm-ctrl font-size" />
                                    <label><?php echo esc_html_e( 'Size', 'devmonsta' ); ?></label>
                                </li>
                                <?php
                            break;
                            case 'line-height':
                                ?>
                                <li>
                                    <input type="number" name="<?php echo esc_attr( $name ) ?>[line_height]" value="<?php echo isset( $value["line_height"] ) && is_numeric( $value["line_height"] ) ? esc_html( trim( $value["line_height"] ) ) : 0.00; ?>"   id="line_height_value" class="devm-option-input devm-ctrl " />
                                    <label><?php echo esc_html_e( 'Line height', 'devmonsta' ); ?></label>
                                </li>
                                <?php
                            break;
                            case 'letter-spacing':
                                ?>
                                <li>
                                    <input type="number" name="<?php echo esc_attr( $name ) ?>[letter_spacing]" value="<?php echo isset( $value["letter_spacing"] ) && is_numeric( $value["letter_spacing"] ) ? esc_html( trim( $value["letter_spacing"] ) ) : 0.00; ?>" id="latter_spacing_value" class="devm-option-input devm-ctrl " />
                                    <label><?php echo esc_html_e( 'Later space', 'devmonsta' ); ?></label>
                                </li>
                                <?php
                            break;
                            case 'color':
                                ?>
                                <li>
                                    <input  type="text" name="<?php echo esc_attr( $name ) ?>[color]" value="<?php echo isset( $value["color"] ) && preg_match('/^#[a-f0-9]{6}$/i', $value["color"]) ? esc_html( $value["color"] ) : ""; ?>"
                                            class="devm-ctrl devm-typography-color-field" data-default-color="<?php echo isset( $value["color"] ) && preg_match('/^#[a-f0-9]{6}$/i', $value["color"]) ? esc_html( $value["color"] ) : ""; ?>" />
                                    <label><?php echo esc_html_e( 'Color', 'devmonsta' ); ?></label>
                                </li>
                                <?php
                            break;

                            default:
                                # code...
                                break;
                            }

                        }

                    }

                }

                // end foreach
                ?>
                </ul>
                <p class="devm-option-desc"><?php echo esc_html( $desc ); ?></p>
            </div>
        </div>
        <?php
    }
}
