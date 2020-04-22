<?php

namespace Devmonsta\Options\Posts\Controls\Select;

use Devmonsta\Options\Posts\Structure;

class Select extends Structure {

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
        wp_enqueue_style( 'select2-css', plugins_url( 'select/assets/css/select2.min.css', dirname( __FILE__ ) ) );
        wp_enqueue_script( 'select2-js', plugins_url( 'select/assets/js/select2.min.js', dirname( __FILE__ ) ) );
        wp_enqueue_script( 'dm-select-js', plugins_url( 'select/assets/js/script.js', dirname( __FILE__ ) ), ['jquery', 'select2-js'], time(), true );

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
        $lable   = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name    = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc    = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs   = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        $choices = isset( $this->content['choices'] ) ? $this->content['choices'] : '';
        $default_attributes = "";

        if ( is_array( $attrs ) && !empty( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {
                $default_attributes .= $key . "='" . $val . "' ";
            }

        }

        ?>

        <div <?php echo esc_attr($default_attributes);?>>
            <lable><?php echo esc_html( $lable ); ?> </lable>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <select id="dm_select" name="<?php echo esc_attr( $this->prefix . $name ); ?>">
                    <?php

        if ( isset( $choices ) ) {

            foreach ( $choices as $key => $val ) {
                $is_selected = ( $key == $this->value ) ? 'selected' : '';
                ?>
                    <option value="<?php echo esc_html( $key ); ?>"
                            <?php echo esc_html( $is_selected ); ?>>
                            <?php echo esc_html( $val ); ?>
                <?php
}

        }

        ?>
            </select>
        </div>
    <?php
}

}
