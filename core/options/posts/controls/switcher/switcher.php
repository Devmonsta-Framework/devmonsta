<?php

namespace Devmonsta\Options\Posts\Controls\Switcher;

use Devmonsta\Options\Posts\Structure;

class Switcher extends Structure {

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
        // js
        wp_enqueue_script( 'dm-switcher', plugins_url( 'switcher/assets/js/dm-switcher.js', dirname( __FILE__ ) ), ['jquery'], time(), true );
        //css
        wp_enqueue_style( 'dm-switcher', plugins_url( 'switcher/assets/css/dm-switcher.css', dirname( __FILE__ ) ) );
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
        $label         = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name          = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc          = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $value         = isset( $this->content['value'] ) ? $this->content['value'] : '';
        $left_choice   = isset( $this->content['left-choice'] ) ? $this->content['left-choice'] : '';
        $right_choice  = isset( $this->content['right-choice'] ) ? $this->content['right-choice'] : '';
        $checked_value = $this->value == '' ? $value : $this->value;
        $checked       = $checked_value == $value ? 'checked' : '';
        $left_key      = '';
        $right_key     = '';

        foreach ( $left_choice as $key => $value ) {
            $left_key .= $key;
        }

        foreach ( $right_choice as $key => $value ) {
            $right_key .= $key;
        }

        // add inline css for dynamic value
        $style = '';
        $style .= '
        .dm_switcher_item label.dm_switcher_label:before {
            content: "' . esc_attr( $left_choice[$left_key] ) . '";
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #fff;
            font-size: 10px;
            display: inline-block;
            text-transform: uppercase;
            font-weight: 600;
        }
        .dm_switcher_item input.dm-control-input:checked + label.dm_switcher_label:before {
            content: "' . esc_attr( $right_choice[$right_key] ) . '";
            right: inherit;
            left: 10px;
        }
        ';
        wp_register_style( 'dm-switcher-inline-css', false, ['dm-switcher'] );
        wp_enqueue_style( 'dm-switcher-inline-css' );
        wp_add_inline_style( 'dm-switcher-inline-css', $style );

        echo "<div>" . esc_html( $label ) . "</div>";
        echo "<div><small>" . esc_html( $desc ) . "</small></div>";
        echo "<div class='dm_switcher_main_block'>";
        echo "<div class='dm_switcher_item'>";
        echo "<input id='dm_switcher_right' type='checkbox' value='" . esc_attr( $right_key ) . "' class='dm-control-input' name='" . esc_attr( $this->prefix . $name ) . "' " . $checked . " />";
        echo "<label  class='dm_switcher_label dm-option-label'></label>";
        echo "</div>";
        echo "<input id='dm_switcher_left' type='checkbox' value='" . esc_attr( $left_key ) . "' class='' name='" . esc_attr( $this->prefix . $name ) . "' checked />";
        echo "</div>";
    }

}
