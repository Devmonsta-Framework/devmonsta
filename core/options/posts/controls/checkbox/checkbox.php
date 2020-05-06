<?php

namespace Devmonsta\Options\Posts\Controls\Checkbox;

use Devmonsta\Options\Posts\Structure;

class Checkbox extends Structure {

    protected $current_screen;

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
        global $wpdocs_admin_page;
        $screen               = get_current_screen();
        $this->current_screen = $screen->base;

        if ( $this->current_screen == "post" ) {
            $content = $this->content;
            global $post;
            $default_value = $content['value'];
            $this->value   = ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                && !empty( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) ?
            get_post_meta( $post->ID, $this->prefix . $content['name'], true )
            : $default_value;
        }

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label      = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $prefix     = 'devmonsta_';
        $name       = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
        $desc       = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs      = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $text       = isset( $this->content['text'] ) ? $this->content['text'] : '';
        $is_checked = ( $this->current_screen == "post" && $this->value == 'true' ) ? 'checked' : '';

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

        $class_attributes = "class='dm-option $dynamic_classes'";
        $default_attributes .= $class_attributes;

        ?>
        <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <label><?php echo esc_html( $label ); ?> </label>
            <input type="text"
                       value="false"
                       id="<?php echo $name; ?>"
                        name="<?php echo esc_attr( $name ); ?>"
                       style="display: none">

            <div><small><?php echo esc_html( $desc ); ?> </small></div>
                <input type="checkbox"
                        id="<?php echo $name; ?>"
                        name="<?php echo esc_attr( $name ); ?>"
                        value="true" <?php echo esc_attr( $is_checked ); ?>>
                        <?php echo esc_html( $text ); ?>
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
                    echo esc_html(  ( get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) == true ) ? "yes" : "no" );
                }

                return $content;

            }, 10, 3 );

    }

    public function edit_fields( $term, $taxonomy ) {
        $prefix             = 'devmonsta_';
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $text               = isset( $this->content['text'] ) ? $this->content['text'] : '';
        $value              = get_term_meta( $term->term_id, $name, true );
        $default_attributes = "";
        $dynamic_classes    = "";
        $is_checked         = ( $value == 'true' ) ? 'checked' : '';

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
                <label for="feature-group"><?php echo esc_html( $label ); ?></label>
            </th>
            <td>
                <input type="text"
                            value="false"
                            name="<?php echo esc_attr( $name ); ?>"
                            style="display: none">
                <input type="checkbox"
                                name="<?php echo esc_attr( $name ); ?>"
                                value="true" <?php echo esc_attr( $is_checked ); ?>>
                                <?php echo esc_html( $text ); ?>
                <br><small>(<?php echo esc_html( $desc ); ?> )</small>
            </td>
        </tr>
    <?php
}

}
