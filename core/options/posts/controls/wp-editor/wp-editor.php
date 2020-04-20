<?php

namespace Devmonsta\Options\Posts\Controls\WpEditor;

use Devmonsta\Options\Posts\Structure;

class WpEditor extends Structure {

    protected $value;

    /**
     * @internal
     */
    public function init() {

    }

    /**
     * @internal
     */
    public function enqueue() {
        add_action( 'admin_enqueue_scripts', [$this, 'load_scripts'] );
    }

    /**
     * @internal
     */
    public function load_scripts( $hook ) {
        wp_enqueue_script( 'dm-wpeditor-js', plugins_url( 'wp-editor/assets/js/script.js', dirname( __FILE__ ) ) );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;

        $this->value = !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ?
        get_post_meta( $post->ID, $this->prefix . $content['name'], true )
        : $content['value'];
        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $lable = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name  = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc  = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs = isset( $this->content['attr'] ) ? $this->content['attr'] : '';

        $settings                  = [];
        $settings["wpautop"]       = ( isset( $this->content['wpautop'] ) ) ? $this->content['wpautop'] : false;
        $settings["editor_height"] = ( isset( $this->content['editor_height'] ) ) ? (int) $this->content['editor_height'] : 425;

        ob_start();
        wp_editor( $this->value, $this->prefix . $name, $settings );
        $editor_html = ob_get_contents();
        ob_end_clean();

        $settings["attr"]["data-size"] = ( isset( $this->content['size'] ) ) ? $this->content['size'] : "small";
        $settings["attr"]["data-mode"] = ( isset( $this->content['editor_type'] ) ) ? $this->content['editor_type'] : false;

        echo dm_html_tag( 'div', $settings["attr"], $editor_html );
    }

}
