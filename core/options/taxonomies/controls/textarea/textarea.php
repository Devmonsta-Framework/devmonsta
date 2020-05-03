<?php

namespace Devmonsta\Options\Taxonomies\Controls\Textarea;

use Devmonsta\Options\Taxonomies\Structure;

class Textarea extends Structure {

    /**
     * @internal
     */
    public function init() {

    }

    /**
     * @internal
     */
    public function enqueue() {

    }

    /**
     * @internal
     */
    public function render() {
        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $prefix = 'devmonsta_';
        $label  = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name   = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
        $desc   = isset( $this->content['desc'] ) ? $this->content['desc'] : '';

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

        $class_attributes = "class='dm-option $dynamic_classes'";
        $default_attributes .= $class_attributes;

        ?>
        <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <label><?php echo esc_html( $label ); ?> </label>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <textarea name="<?php echo esc_attr( $name ); ?>"></textarea>
        </div<>
    <?php
}

    public function columns() {
        $visible = true;
        $content = $this->content;
        add_filter( 'manage_edit-' . $this->taxonomy . '_columns', function ( $columns ) use ( $content, $visible ) {

            if ( isset( $content['show_in_table'] ) ) {

                if ( $content['show_in_table'] == false ) {
                    $visible = false;
                }

            }

            if ( $visible ) {
                $columns[$content['name']] = __( $content['label'], 'devmonsta' );
            }

            return $columns;
        } );

        $cc = $content;
        add_filter( 'manage_' . $this->taxonomy . '_custom_column', function ( $content, $column_name, $term_id ) use ( $cc ) {

            if ( $column_name == $cc['name'] ) {
                print_r( get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) );

            }

            return $content;

        }, 10, 3 );

    }

    public function edit_fields( $term, $taxonomy ) {
        $prefix = 'devmonsta_';
        $label  = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name   = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
        $desc   = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs  = isset( $this->content['attr'] ) ? $this->content['attr'] : '';

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
        $value = get_term_meta( $term->term_id, $name, true );
        ?>

    <tr <?php echo dm_render_markup( $default_attributes ); ?> >
        <th scope="row"><label for="feature-group"><?php echo esc_html( $label ); ?></label></th>
        <td> <textarea name="<?php echo esc_attr( $name ); ?>"><?php echo $value; ?></textarea>
    </tr>
    <?php
}

}
