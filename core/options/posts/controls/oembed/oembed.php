<?php

namespace Devmonsta\Options\Posts\Controls\Oembed;

use Devmonsta\Options\Posts\Structure;

class Oembed extends Structure {

    /**
     * @internal
     */
    public function init() {
    }

    public function enqueue() {
        add_action( 'init', [$this, 'enqueue_oembed_scripts'] );
        add_action(
            'wp_ajax_get_oembed_response',
            [$this, '_action_get_oembed_response']
        );
    }

    public function enqueue_oembed_scripts() {
        wp_register_script( 'dm-oembed', DM_CORE . 'options/posts/controls/oembed/assets/js/script.js', ['underscore', 'wp-util'], time(), true );
        wp_localize_script( 'dm-oembed', 'object', ['ajaxurl' => admin_url( 'admin-ajax.php' )] );
        wp_enqueue_script( 'dm-oembed' );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;
        $this->value = ( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) !== "" &&
            !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) ?
        get_post_meta( $post->ID, $this->prefix . $content['name'], true )
        : $content['value'];
        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name  = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc  = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs = isset( $this->content['attr'] ) ? $this->content['attr'] : '';

        $wrapper_attr['data-nonce']   = wp_create_nonce( '_action_get_oembed_response' );
        $wrapper_attr['data-preview'] = json_encode( $this->content['preview'] );

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
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
        </div>
        <div class="dm-oembed-input">
            <input <?php echo dm_attr_to_html( $wrapper_attr ) ?>
                    type="url" name="<?php echo esc_attr( $this->prefix . $name ); ?>"
                    value="<?php echo esc_html( $this->value ); ?>"
                    class="dm-oembed-url-input"/>
        </div>
        <div class="dm-oembed-preview">
        </div<>
    <?php
}

    public function _action_get_oembed_response() {

        if ( wp_verify_nonce( \DM_Request::POST( '_nonce' ), '_action_get_oembed_response' ) ) {

            require_once DM_CORE . 'helpers/class-dm-request.php';
            $url = \DM_Request::POST( 'url' );

            $width = \DM_Request::POST( 'preview/width' );

            $height = \DM_Request::POST( 'preview/height' );

            $keep_ratio = ( \DM_Request::POST( 'preview/keep_ratio' ) === 'true' );

            $iframe = empty( $keep_ratio ) ?

            dm_oembed_get( $url, compact( 'width', 'height' ) ) :

            wp_oembed_get( $url, compact( 'width', 'height' ) );

            echo $iframe;
            die();

        } else {
            echo 'Invalid nonce';
            die();
        }

    }

}
