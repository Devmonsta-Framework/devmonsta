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
        add_action( 'init', [$this, 'dm_enqueue_color_picker'] );
    }

    /**
     * @internal
     */
    function dm_enqueue_color_picker() {

        if ( !wp_style_is( 'wp-color-picker', 'enqueued' ) ) {
            wp_enqueue_style( 'wp-color-picker' );
        }

        if ( !wp_script_is( 'dm-script-handle', 'enqueued' ) ) {
            wp_enqueue_script( 'dm-script-handle', DM_CORE . 'options/posts/controls/color-picker/assets/js/script.js', ['jquery', 'wp-color-picker'], false, true );
        }

        $data             = [];
        $data['default']  = $this->content['value'];
        $data['palettes'] = isset( $this->content['palettes'] ) ? $this->content['palettes'] : false;
        wp_localize_script( 'dm-script-handle', 'color_picker_config', $data );
    }

    public function render() {
        $this->output();
    }

    public function output() {
        $prefix             = 'devmonsta_';
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $prefix . $this->content['name'] : '';
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

        $class_attributes = "class='dm-option $dynamic_classes'";
        $default_attributes .= $class_attributes;

        ?>
        <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <label><?php echo esc_html( $label ); ?> </label>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <input  type="text"
                    name="<?php echo esc_attr( $name ); ?>"
                    class="dm-color-field"
                    data-default-color="<?php echo esc_attr( $this->content['value'] ); ?>" />
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

    }

}
