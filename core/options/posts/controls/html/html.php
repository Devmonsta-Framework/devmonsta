<?php

namespace Devmonsta\Options\Posts\Controls\Html;

use Devmonsta\Options\Posts\Structure;

class Html extends Structure {

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
        $label        = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name         = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc         = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $html         = isset( $this->content['html'] ) ? $this->content['html'] : '';
        echo "<div>".esc_html( $label )."</div>";
        echo "<div><small>".esc_html( $desc )."</small></div>";
        echo "<div class='dm_html_block'>";
              echo htmlspecialchars_decode(esc_html( $html ));
        echo "</div>";
    }

}
