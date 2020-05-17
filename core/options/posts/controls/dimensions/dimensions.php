<?php

namespace Devmonsta\Options\Posts\Controls\Dimensions;

use Devmonsta\Options\Posts\Structure;

class Dimensions extends Structure {

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
        $default_value = isset( $content['value'] ) ? $content['value'] : [];
        $this->value   = (  ( $this->current_screen == "post" )
            && ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
            && ( "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
        ? maybe_unserialize( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
        : $default_value;

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
                <dm-dimensions
                    :dimension="<?php echo isset( $this->value["isLinked"] ) ? esc_attr( $this->value["isLinked"] ) : 'false'; ?>" linked-name="<?php echo esc_attr( $name ); ?>[isLinked]"
                >
                    <dm-dimensions-item
                        name="<?php echo esc_attr( $name ); ?>[top]"
                        value="<?php echo isset( $this->value["top"] ) ? esc_html( intval( $this->value["top"] ) ) : 0; ?>"
                        label="top"
                    ></dm-dimensions-item>

                    <dm-dimensions-item
                        name="<?php echo esc_attr( $name ); ?>[right]"
                        value="<?php echo isset( $this->value["right"] ) ? esc_html( intval( $this->value["right"] ) ) : 0; ?>"
                        label="right"
                    ></dm-dimensions-item>

                    <dm-dimensions-item
                        name="<?php echo esc_attr( $name ); ?>[bottom]"
                        value="<?php echo isset( $this->value["bottom"] ) ? esc_html( intval( $this->value["bottom"] ) ) : 0; ?>"
                        label="bottom"
                    ></dm-dimensions-item>

                    <dm-dimensions-item
                        name="<?php echo esc_attr( $name ); ?>[left]"
                        value="<?php echo isset( $this->value["left"] ) ? esc_html( intval( $this->value["left"] ) ) : 0; ?>"
                        label="left"
                    ></dm-dimensions-item>
                </dm-dimensions>
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
                    $values = maybe_unserialize( get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) );

                    if ( is_array( $values ) && !empty( $values ) ) {

                        foreach ( $values as $key => $value ) {
                            echo $key . ": " . $value . "<br>";
                        }

                    }

                }

                return $content;

            }, 10, 3 );

    }

    public function edit_fields( $term, $taxonomy ) {
        $this->enqueue_dimensions_scripts();

        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $value              = (  ( !is_null( get_term_meta( $term->term_id, $name, true ) ) ) && ( "" != get_term_meta( $term->term_id, $name, true ) ) ) ? maybe_unserialize( get_term_meta( $term->term_id, $name, true ) ) : [];
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
        <ul class="dm-option-dimensions">
                    <li>
                        <input class="dm-option-input dm-dimension-number-input input-top" type="number" name="<?php echo esc_attr( $name ); ?>[top]" value="<?php echo isset( $this->value["top"] ) ? esc_html( intval( $this->value["top"] ) ) : 0; ?>" min="0"/>
                        <label><?php esc_html_e( "top", "devmonsta" );?></label>
                    </li>
                    <li>
                        <input class="dm-option-input dm-dimension-number-input input-right" type="number" name="<?php echo esc_attr( $name ); ?>[right]" value="<?php echo isset( $this->value["right"] ) ? esc_html( intval( $this->value["right"] ) ) : 0; ?>"  min="0"/>
                        <label><?php esc_html_e( "right", "devmonsta" );?></label>
                    </li>
                    <li>
                        <input class="dm-option-input dm-dimension-number-input input-bottom" type="number" name="<?php echo esc_attr( $name ); ?>[bottom]" value="<?php echo isset( $this->value["bottom"] ) ? esc_html( intval( $this->value["bottom"] ) ) : 0; ?>"  min="0"/>
                        <label><?php esc_html_e( "bottom", "devmonsta" );?></label>
                    </li>
                    <li>
                        <input class="dm-option-input dm-dimension-number-input input-left" type="number" name="<?php echo esc_attr( $name ); ?>[left]" value="<?php echo isset( $this->value["left"] ) ? esc_html( intval( $this->value["left"] ) ) : 0; ?>"  min="0"/>
                        <label><?php esc_html_e( "left", "devmonsta" );?></label>
                    </li>
                    <li>
                        <input class="dm-dimension-linked-input" type="hidden" name="<?php echo esc_attr( $name ); ?>[isLinked]" value="<?php echo isset( $this->value["isLinked"] ) ? esc_html( intval( $this->value["isLinked"] ) ) : 0; ?>"/>
                        <button class="dm-dimension-attachment-input <?php echo intval( $this->value["isLinked"] ) == 1 ? 'clicked' : ''; ?>" style="cursor:pointer; width:50px; height: 30px; border: 1px solid gray;"></button>
                        <label>&nbsp;</label>
                    </li>
        </ul>
        <br><small class="dm-option-desc">(<?php echo esc_html( $desc ); ?> )</small>
    </td>
    </tr>
<?php
}

}
