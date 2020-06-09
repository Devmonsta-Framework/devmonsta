<?php
namespace Devmonsta\Options\Customizer\Controls\Typography;

use Devmonsta\Options\Customizer\Structure;

class Typography extends Structure {

    public $label, $name, $desc, $default_value, $value, 
            $components, $font_list, $default_attributes;

    /**
     * @access public
     * @var    string
     */
    public $type = 'typography';

    public $statuses;

    public function __construct( $manager, $id, $args = [] ) {

        $this->prepare_values( $id, $args );
        $this->statuses = ['' => __( 'Default' )];
        parent::__construct( $manager, $id, $args );
    }

    public function prepare_values( $id, $args = [] ) {
        $this->label         = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name          = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc          = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
        $this->default_value = isset( $args[0]['value'] ) && is_array( $args[0]['value'] ) ? $args[0]['value'] : [];
        $this->components    = isset( $args[0]['components'] ) && is_array( $args[0]['components'] ) ? $args[0]['components'] : [];
    
        //generate attributes dynamically for parent tag
        $this->default_attributes = $this->prepare_default_attributes( $args[0] );
    }

    /**
     * @internal
     */
    public function enqueue() {
        if ( !wp_style_is( 'wp-color-picker', 'enqueued' ) ) {
            wp_enqueue_style( 'wp-color-picker' );
        }
        wp_enqueue_script( 'dm-customizer-typo-script-handle', DM_CORE . 'options/customizer/controls/typography/assets/js/scripts.js', ['jquery', 'wp-color-picker'], false, true );
        $this->value            = !empty( $this->value() ) ? (array) json_decode($this->value()) : $this->default_value;
        $this->font_list        = $this->dm_getGoogleFonts();
        $data['font_list']      = $this->font_list;
        $data['selected_data']  = $this->value;
        wp_localize_script( 'dm-customizer-typo-script-handle', 'typo_config', $data );
    }

    /**
     * @internal
     */
    public function render() {
        $this->render_content();
    }

    public function render_content() {
        
        ?>
        <li <?php echo dm_render_markup( $this->default_attributes ); ?>>
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="dm-option-column right full-width">
                <ul class="dm-option-typography">
                <?php

                if ( is_array( $this->components ) && !empty( $this->components ) ) {
                    foreach ( $this->components as $key => $item ) {
                        if ( $key ) {
                            switch ( $key ) {
                            case 'family':
                                ?>
                                <li class="typo-font-list">
                                    <?php
                                    if ( is_array($this->font_list) && count( $this->font_list ) > 0 ): ?>
                                        <div class="google-fonts">
                                            <select class="dm-ctrl google-fonts-list" name="<?php echo esc_attr( $this->name ) ?>[family]">
                                            <?php
                                            foreach ( $this->font_list as $key => $item ) {
                                                $selected = ( $item->family == esc_html( $this->value["family"] ) ) ? 'selected' : '';
                                                echo '<option value="' . $item->family . '" ' . $selected . '>' . $item->family . '</option>';
                                            }
                                            ?>
                                            </select>
                                        </div>
                                    <?php endif;?>
                                    <?php echo esc_html_e( 'Family', 'devmonsta' ); ?>
                                </li>
                                <li class="typo-font-weight">
                                    <select name="<?php echo esc_attr( $this->name ) ?>[weight]" class="dm-option-input dm-ctrl google-weight-list"></select>
                                    <label><?php echo esc_html_e( 'Weight', 'devmonsta' ); ?></label>
                                </li>
                                <li class="typo-font-style">
                                    <select name="<?php echo esc_attr( $this->name ) ?>[style]" class="dm-option-input dm-ctrl google-style-list">
                                    </select>
                                    <label><?php echo esc_html_e( 'Style', 'devmonsta' ); ?></label>
                                </li>
                                <?php
                            break;
                            case 'size':
                                ?>
                                <li class="typo-font-size">
                                    <input type="number" value="<?php echo isset( $this->value["size"] ) ? esc_html( trim( $this->value["size"] ) ) : 0.00; ?>" class="dm-option-input dm-ctrl typo-font-size" />
                                    <label><?php echo esc_html_e( 'Size', 'devmonsta' ); ?></label>
                                </li>
                                <?php
                            break;
                            case 'line-height':
                                ?>
                                <li class="typo-font-lineheight">
                                    <input type="number" value="<?php echo isset( $this->value["line_height"] ) ? esc_html( trim( $this->value["line_height"] ) ) : 0.00; ?>" class="dm-option-input dm-ctrl typo-font-line-height" />
                                    <label><?php echo esc_html_e( 'Line height', 'devmonsta' ); ?></label>
                                </li>
                                <?php
                            break;
                            case 'letter-spacing':
                                ?>
                                <li class="typo-font-laterspace">
                                    <input type="number" value="<?php echo isset( $this->value["letter_spacing"] ) ? esc_html( trim( $this->value["letter_spacing"] ) ) : 0.00; ?>" class="dm-option-input dm-ctrl typo-font-letter-space" />
                                    <label><?php echo esc_html_e( 'Later space', 'devmonsta' ); ?></label>
                                </li>
                                <?php
                            break;
                            case 'color':
                                ?>
                                <li class="typo-font-color">
                                    <input  type="text" name="<?php echo esc_attr( $this->name ) ?>[color]"
                                            value="<?php echo isset( $this->value["color"] ) ? esc_html( $this->value["color"] ) : ""; ?>"
                                            class="dm-ctrl dm-typography-color-field"
                                            data-default-color="<?php echo esc_attr( $this->value["color"] ); ?>" />
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
                    <li><input type="hidden" class="input-typo-value-holder" <?php $this->link(); ?> value="" /></li>
                </ul>
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>

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
