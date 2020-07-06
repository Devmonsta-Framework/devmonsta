<?php
namespace Devmonsta\Options\Taxonomies\Controls\ColorPicker;

use Devmonsta\Options\Taxonomies\Structure;

class ColorPicker extends Structure {
    public function init() {

    }

    /**
     * @internal
     */
    public function enqueue() {
        add_action( 'init', [$this, 'devm_enqueue_color_picker'] );
    }

    /**
     * @internal
     */
    function devm_enqueue_color_picker() {
        if ( !wp_style_is( 'wp-color-picker', 'enqueued' ) ) {
            wp_enqueue_style( 'wp-color-picker' );
        }

        if ( !wp_script_is( 'devm-script-handle', 'enqueued' ) ) {
            wp_enqueue_script( 'devm-script-handle', DEVMONSTA_CORE . 'options/posts/controls/color-picker/assets/js/script.js', ['jquery', 'wp-color-picker'], time(), true );
        }

        $data            = [];
        $data['default'] = $this->content['value'];
        $data['palettes'] = isset( $this->content['palettes'] ) ? $this->content['palettes'] : false;
        wp_localize_script( 'devm-script-handle', 'color_picker_config', $data );
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
        $prefix             = 'devmonsta_';
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $default            = isset( $this->content['value'] ) ? $this->content['value'] : '#000000';
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

        $class_attributes = "class='devm-option $dynamic_classes'";
        $default_attributes .= $class_attributes;

        ?>
        <div <?php echo devm_render_markup( $default_attributes ); ?> >
            <label><?php echo esc_html( $label ); ?> </label>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <input  type="text"
                    name="<?php echo esc_attr( $name ); ?>"
                    class="devm-color-field"
                    data-default-color="<?php echo esc_attr( $default ); ?>" />
        </div>
    <?php
}

    /**
     * @internal
     */
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
                $columns[$content['name']] =esc_html__( $content['label'], 'devmonsta' );
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

    /**
     * @internal
     */
    public function edit_fields( $term, $taxonomy ) {
        //enqueue scripts and styles for color picker
        $this->devm_enqueue_color_picker();
        $prefix             = 'devmonsta_';
        $name               = $prefix . $this->content['name'];
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

        $class_attributes = "class='devm-option term-group-wrap $dynamic_classes'";
        $default_attributes .= $class_attributes;

        ?>

        <tr <?php echo devm_render_markup( $default_attributes ); ?> >
            <th scope="row"><label for="feature-group"><?php echo esc_html( $this->content['label'] ); ?></label></th>
            <td> <input  type="text"
                    name="<?php echo esc_attr( $name ); ?>"
                    value="<?php echo esc_attr( $value ); ?>"
                    class="devm-color-field"
                    data-default-color="#FF0000" />
            </td>
        </tr>
        <?php
}

}
