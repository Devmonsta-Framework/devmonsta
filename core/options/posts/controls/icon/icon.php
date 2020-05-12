<?php

namespace Devmonsta\Options\Posts\Controls\Icon;

use Devmonsta\Options\Posts\Structure;

class Icon extends Structure {

    /**
     * @internal
     */
    public function init() {

    }

    /**
     * @internal
     */
    public function enqueue($current_screen) {
        add_action( 'init', [$this, 'enqueue_icon_scripts'] );
    }

    public function enqueue_icon_scripts() {
        include 'icon-data.php';

        wp_enqueue_style( 'dm-fontawesome-css', DM_CORE . 'options/posts/controls/icon/assets/css/font-awesome.min.css' );
        wp_enqueue_style( 'dm-main-css', DM_CORE . 'options/posts/controls/icon/assets/css/main.css' );
        wp_enqueue_script( 'vue-js', 'https://cdn.jsdelivr.net/npm/vue/dist/vue.js' );
        wp_enqueue_script( 'dm-asicon', DM_CORE . 'options/posts/controls/icon/assets/js/script.js', ['jquery', 'vue-js'], time(), true );
        wp_localize_script( 'dm-asicon', 'dmIcons', $iconList );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;

        $this->value = !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ?
        get_post_meta( $post->ID, $this->prefix . $content['name'], true )
        : "";
        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        include 'icon-data.php';

        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->content['name'] : '';
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

        $class_attributes = "class='dm-vue-app dm-option form-field $dynamic_classes'";
        $default_attributes .= $class_attributes;
        $iconEncoded = json_encode($iconList);
        ?>
        
        <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $label ); ?></label>
            </div>
            <div class="dm-option-column right">
                <dm-icon-picker
                    name='<?php echo esc_attr( $this->prefix . $name ); ?>'
                    icon_list='<?php echo $iconEncoded; ?>'
                    default_icon_type='dm-font-awesome'
                ></dm-icon-picker>
                <p class="dm-option-desc"><?php echo esc_html( $desc ); ?> </p>
            </div>
        </div>
    <?php
}

public function columns() {

}

public function edit_fields( $term, $taxonomy ) {
}

}
