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
        $attrs        = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $html         = isset( $this->content['html'] ) ? $this->content['html'] : '';
        
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
            <lable class="dm-option-label"><?php echo esc_html( $label ); ?> </lable>
            <div><small class="dm-option-desc"><?php echo esc_html( $desc ); ?></small></div>
            <div class='dm_html_block'>
                <?php echo htmlspecialchars_decode(esc_html( $html ));?>
            </div>
        </div>
    }

}
