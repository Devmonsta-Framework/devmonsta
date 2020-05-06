<?php

namespace Devmonsta\Options\Posts\Controls\Multiselect;

use Devmonsta\Options\Posts\Structure;

class Multiselect extends Structure {

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

        add_action( 'init', [$this, 'load_multi_select_scripts'] );

    }

    /**
     * @internal
     */
    public function load_multi_select_scripts() {
        wp_enqueue_style( 'select2-css', DM_CORE . 'options/posts/controls/multiselect/assets/css/select2.min.css' );
        wp_enqueue_script( 'select2-js', DM_CORE . 'options/posts/controls/multiselect/assets/js/select2.min.js' );
        wp_enqueue_script( 'dm-multiselect-js', DM_CORE . 'options/posts/controls/multiselect/assets/js/script.js', ['jquery', 'select2-js'], time(), true );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;

        if ( $this->current_screen == "post" ) {
            global $post;

            if ( !empty( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
                && !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) {
                $this->value = maybe_unserialize( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) );
            }

        }

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $prefix             = 'devmonsta_';
        $name               = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $choices            = isset( $this->content['choices'] ) ? $this->content['choices'] : '';
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
            <label  class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <select id="dm_multi_select" multiple="multiple" name="<?php echo esc_attr( $name ); ?>[]">
        <?php

        if ( isset( $choices ) ) {

            foreach ( $choices as $key => $val ) {

                if ( $this->current_screen == "post" && is_array( $this->value ) && in_array( $key, $this->value ) ) {
                    $selected = 'selected';
                } else {
                    $selected = null;
                }

                ?>
                    <option value="<?php echo esc_attr( $key ); ?>"
                            <?php echo esc_html( $selected ); ?>>
                            <?php echo esc_html( $val ); ?>
        <?php
}

        }

        ?>
            </select>
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
                    $choices        = isset( $content['choices'] ) ? $content['choices'] : '';
                    $selected_value = maybe_unserialize( get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) );
                    $selected_data  = [];

                    if ( isset( $choices ) ) {

                        foreach ( $choices as $key => $val ) {

                            if ( is_array( $selected_value ) && in_array( $key, $selected_value ) ) {
                                array_push( $selected_data, $val );
                            }

                        }

                        echo esc_html( join( ", ", $selected_data ) );
                    }

                }

                return $content;

            }, 10, 3 );

    }

    public function edit_fields( $term, $taxonomy ) {

        $this->load_multi_select_scripts();

        $prefix             = 'devmonsta_';
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $value              = maybe_unserialize( get_term_meta( $term->term_id, $name, true ) );
        $choices            = isset( $this->content['choices'] ) ? $this->content['choices'] : '';
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

        <select id="dm_select" name="<?php echo esc_attr( $name ); ?>[]" multiple="multiple" >
    <?php

        if ( isset( $choices ) ) {

            foreach ( $choices as $key => $val ) {

                if ( is_array( $value ) && in_array( $key, $value ) ) {
                    $selected = 'selected';
                } else {
                    $selected = null;
                }

                ?>
                    <option value="<?php echo esc_attr( $key ); ?>"
                            <?php echo esc_html( $selected ); ?>>
                            <?php echo esc_html( $val ); ?>
        <?php
}

        }

        ?>
            </select>

        <br><small>(<?php echo esc_html( $desc ); ?> )</small>
    </td>
</tr>
<?php
}

}
