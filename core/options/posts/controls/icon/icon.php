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
        wp_enqueue_style( 'dm-fontawesome-css', DM_CORE . 'options/posts/controls/icon/assets/css/font-awesome.min.css' );
        wp_enqueue_script( 'vue-js', 'https://cdn.jsdelivr.net/npm/vue' );
        wp_enqueue_script( 'dm-asicon', DM_CORE . 'options/posts/controls/icon/assets/js/script.js', ['jquery'], time(), true );
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
        $label              = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name               = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc               = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs              = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $default_attributes = "";
        $dynamic_classes    = "";

        $iconList = [
                [
                    "name" => "Font Awesome v5.0.1",
                    "icons" => [
                        "fab fa-500px",
                        "fab fa-accessible-icon",
                        "fab fa-accusoft",
                        "fas fa-address-book", "far fa-address-book",
                        "fas fa-address-card", "far fa-address-card",
                        "fas fa-adjust",
                        "fab fa-adn",
                        "fab fa-adversal",
                        "fab fa-affiliatetheme",
                        "fab fa-algolia",
                        "fas fa-align-center",
                        "fas fa-align-justify",
                        "fas fa-align-left",
                        "fas fa-align-right",
                    ]
                ]
        ];

        if ( is_array( $attrs ) && !empty( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {

                if ( $key == "class" ) {
                    $dynamic_classes .= $val . " ";
                } else {
                    $default_attributes .= $key . "='" . $val . "' ";
                }

            }

        }

        $class_attributes = "class='dm-vue-app dm-option $dynamic_classes'";
        $default_attributes .= $class_attributes;

        ?>
        
        <div <?php echo dm_render_markup( $default_attributes ); ?> >
            <label class="dm-option-label"><?php echo esc_html( $label ); ?> {{ message }}</label>
            <div><small class="dm-option-desc"><?php echo esc_html( $desc ); ?> </small></div>
            <select id="default" name="<?php echo esc_attr( $this->prefix . $name ); ?>" class="dm-icon-picker">
                            <option value="fa-search">Search</option>
                            <option value="fa-star">Star</option>
                            <option value="fa-times">Times</option>
                            <option value="fa-refresh">Refresh</option>
                            <option value="fa-rocket">Rocket</option>
                            <option value="fa-bookmark">Bookmark</option>
                            <option value="fa-heart">Heart</option>
                            <option value="fa-adn">Adn</option>
                            <option value="fa-cloud-upload">Cloud-upload</option>
                            <option value="fa-phone-square">Phone-square</option>
                            <option value="fa-caret-right">Caret-right</option>
                            <option value="fa-caret-down">Caret-down</option>
                            <option value="fa-caret-up">Caret-up</option>
                            <option value="fa-caret-left">Caret-left</option>
                            <option value="fa-eye">Eye</option>
                            <option value="fa-tag">Tag</option>
                            <option value="fa-cog">Cog</option>
                            <option value="fa-wrench">Wrench</option>
                            <option value="fa-volume-down">Volume-down</option>
                            <option value="fa-thumbs-up">Thumbs-up</option>
            </select>
        </div>
    <?php
}

public function columns() {

}

public function edit_fields( $term, $taxonomy ) {
}

}
