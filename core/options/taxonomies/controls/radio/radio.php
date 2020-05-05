<?php

namespace Devmonsta\Options\Taxonomies\Controls\Radio;

use Devmonsta\Options\Taxonomies\Structure;

class Radio extends Structure {

    protected $choices;

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
        $prefix  = 'devmonsta_';
        $label   = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name    = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
        $desc    = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs   = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $this->choices = isset( $this->content['choices'] ) ? $this->content['choices'] : '';

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
            <div>
                <small><?php echo esc_html( $desc ); ?> </small>
            </div>
            <?php

        if ( isset( $this->choices ) ) {

            foreach ( $this->choices as $key => $val ) {
                ?>
                <input type="radio"
                        name="<?php echo esc_attr( $name ); ?>"
                        value="<?php echo esc_attr( $key ); ?>"
                        ><?php echo $val; ?>
                <?php
}

        }

        ?>
        </div>
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
                $saved_value = get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) ;
                foreach($this->choices as $key => $value){
                    if($saved_value == $key){
                        echo $value;
                    }
                }

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
        $choices            = isset( $this->content['choices'] ) ? $this->content['choices'] : '';
        $value              = get_term_meta( $term->term_id, $name, true );
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
                <label for="feature-group"><?php echo esc_html( $label ); ?></label>
            </th>
            <td>
                <?php

        if ( isset( $choices ) ) {

            foreach ( $choices as $key => $val ) {
                $is_checked = ( $key == $value ) ? 'checked' : '';
                ?>
                        <input type="radio"
                                name="<?php echo esc_attr( $name ); ?>"
                                value="<?php echo esc_attr( $key ); ?>"
                                <?php echo esc_html( $is_checked ); ?>>
                                <?php echo esc_html( $val ); ?>
                        <?php
}

        }

        ?>

            <br>(<?php echo esc_html( $desc ); ?>)
            </td>
        </tr>
        <?php
}

}
