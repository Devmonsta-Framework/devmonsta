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
    public function enqueue() {
        add_action( 'init', [$this, 'enqueue_icon_scripts'] );
    }

    public function enqueue_icon_scripts() {
        wp_enqueue_style( 'dm-normalize-css', DM_CORE . 'options/posts/controls/icon/assets/css/normalize.css' );
        // wp_enqueue_style( 'dm-main-css', DM_CORE . 'options/posts/controls/icon/assets/css/main.css' );
        wp_enqueue_style( 'dm-prism-css', DM_CORE . 'options/posts/controls/icon/assets/css/prism.css' );
        wp_enqueue_style( 'dm-asIconPicker-css', DM_CORE . 'options/posts/controls/icon/assets/css/asIconPicker.css' );
        wp_enqueue_style( 'dm-fontawesome-css', DM_CORE . 'options/posts/controls/icon/assets/css/font-awesome.min.css' );
        wp_enqueue_style( 'dm-asTooltip-css', DM_CORE . 'options/posts/controls/icon/assets/css/asTooltip.min.css' );

        wp_enqueue_script( 'dm-toc-js', DM_CORE . 'options/posts/controls/icon/assets/js/jquery.toc.js' );
        wp_enqueue_script( 'dm-prism-js', DM_CORE . 'options/posts/controls/icon/assets/js/prism.js' );
        wp_enqueue_script( 'dm-tooltip-js', DM_CORE . 'options/posts/controls/icon/assets/js/jquery-asTooltip.min.js' );
        wp_enqueue_script( 'dm-scrollbar-js', DM_CORE . 'options/posts/controls/icon/assets/js/jquery-asScrollbar.js' );
        wp_enqueue_script( 'dm-asIconPicker-js', DM_CORE . 'options/posts/controls/icon/assets/js/jquery-asIconPicker.js' );
        wp_enqueue_script( 'dm-asicon', DM_CORE . 'options/posts/controls/icon/assets/js/script.js', ['jquery', 'dm-asIconPicker-js'], time(), true );
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
        </div<>
    <?php
}

}
