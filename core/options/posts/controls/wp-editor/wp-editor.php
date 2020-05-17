<?php

namespace Devmonsta\Options\Posts\Controls\WpEditor;

use Devmonsta\Options\Posts\Structure;

class WpEditor extends Structure {

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
        add_action( 'admin_enqueue_scripts', [$this, 'load_wpeditor_scripts'] );
    }

    /**
     * @internal
     */
    public function load_wpeditor_scripts() {
        wp_enqueue_script( 'dm-wpeditor-js', DM_CORE . 'options/posts/controls/wp-editor/assets/js/script.js', ['jquery'] );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;

        $this->value = (  ( $this->current_screen == "post" )
            && ( !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
            && ( "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) )
        ? get_post_meta( $post->ID, $this->prefix . $content['name'], true )
        : $content['value'];
        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label  = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name   = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc   = isset( $this->content['desc'] ) ? $this->content['desc'] : '';

        $settings                  = [];
        $settings["wpautop"]       = ( isset( $this->content['wpautop'] ) ) ? $this->content['wpautop'] : false;
        $settings["editor_height"] = ( isset( $this->content['editor_height'] ) ) ? (int) $this->content['editor_height'] : 285;
        $settings["tinymce"]       = ( isset( $this->content['editor_type'] ) && $this->content['editor_type'] === false ) ? false : true;

        ob_start();
        ?>
        <div class="dm-option form-field">
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
            </div>

            <div class="dm-option-column right">
                <?php
                        wp_editor( $this->value, $name, $settings );
                        $editor_html = ob_get_contents();
                        $editor_html .= "<p class='dm-option-desc'>" . esc_html( $desc ) . " </p>";
                        ob_end_clean();

                        echo dm_render_markup( $editor_html );
                ?>
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
                    echo esc_html( get_term_meta( $term_id, 'devmonsta_' . $column_name, true ) );
                }

                return $content;

            }, 10, 3 );

    }

    public function edit_fields( $term, $taxonomy ) {
        $this->load_wpeditor_scripts();
        $label  = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name   = isset( $this->content['name'] ) ? $this->prefix . $this->content['name'] : '';
        $desc   = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs  = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $value  = get_term_meta( $term->term_id, $name, true );

        $settings                  = [];
        $settings["wpautop"]       = ( isset( $this->content['wpautop'] ) ) ? $this->content['wpautop'] : false;
        $settings["editor_height"] = ( isset( $this->content['editor_height'] ) ) ? (int) $this->content['editor_height'] : 425;

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
                        ob_start();
                    ?>
                    <div>
                        <label class="dm-option-label"><?php echo esc_html( $label ); ?> </label>
                        <div><small class="dm-option-desc"><?php echo esc_html( $desc ); ?> </small></div>
                        <?php
                            wp_editor( $value, $name, $settings );
                            $editor_html = ob_get_contents();
                            $editor_html .= "<p class='dm-option-desc'>" . esc_html( $desc ) . " </p>";
                            ob_end_clean();

                            echo dm_render_markup( $editor_html );
                    ?>
                <br><small class="dm-option-desc">(<?php echo esc_html( $desc ); ?> )</small>
            </td>
        </tr>
<?php
}

}
