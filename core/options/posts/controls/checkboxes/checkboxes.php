<?php

namespace Devmonsta\Options\Posts\Controls\Checkboxes;

use Devmonsta\Options\Posts\Structure;

class Checkboxes extends Structure {

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
            global $post;
            $content             = $this->content;
            $default_value_array = [];

            if ( is_array( $content['value'] ) && !empty( $content['value'] ) ) {

                foreach ( $content['value'] as $default_key => $default_value ) {

                    if ( $default_value == true ) {
                        array_push( $default_value_array, $default_key );
                    }

                }

            }

            $this->value = ( !empty( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                && !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
            ? maybe_unserialize( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
            : $default_value_array;
        }

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label   = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $prefix  = 'devmonsta_';
        $name    = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
        $desc    = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs   = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $choices = isset( $this->content['choices'] ) ? $this->content['choices'] : '';

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
                <label class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
                <div><small class="dm-option-desc"><?php echo esc_html( $desc ); ?> </small></div>
        <?php

        foreach ( $choices as $id => $element ) {

            if ( $this->current_screen == "post" && is_array( $this->value ) && in_array( $id, $this->value ) ) {
                $checked = 'checked="checked"';
            } else {
                $checked = null;
            }

            ?>
                <input  type="checkbox"
                        id="<?php echo $name; ?>"
                        name="<?php echo esc_attr( $name ); ?>[]"
                        value="<?php echo esc_attr( $id ); ?>" <?php echo esc_attr( $checked ); ?> />
                    <?php echo esc_html( $element ); ?>
            <?php
}

        ?>
        <input type="text" value="default" name="<?php echo esc_attr( $name ); ?>[]" style="display: none">
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
                    echo esc_html(  ( maybe_unserialize( $term_id, 'devmonsta_' . $column_name, true ) == true ) ? "yes" : "no" );
                }

                return $content;

            }, 10, 3 );

    }

    public function edit_fields( $term, $taxonomy ) {
        $prefix  = 'devmonsta_';
        $label   = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name    = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
        $desc    = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs   = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $value   = maybe_unserialize( get_term_meta( $term->term_id, $name, true ) );
        $choices = isset( $this->content['choices'] ) ? $this->content['choices'] : '';

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
            <?php

        foreach ( $choices as $id => $element ) {

            if ( is_array( $value ) && in_array( $id, $value ) ) {
                $checked = 'checked="checked"';
            } else {
                $checked = null;
            }

            ?>
                <input  type="checkbox"
                        id="<?php echo $name; ?>"
                        name="<?php echo esc_attr( $name ); ?>[]"
                        value="<?php echo esc_attr( $id ); ?>" <?php echo esc_attr( $checked ); ?> />
                    <?php echo esc_html( $element ); ?>
            <?php
}

        ?>
                <input type="text" value="default" name="<?php echo esc_attr( $name ); ?>[]" style="display: none">


                <br><small class="dm-option-desc">(<?php echo esc_html( $desc ); ?> )</small>
            </td>
        </tr>
    <?php
}

}
