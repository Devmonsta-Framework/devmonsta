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
        ?>
        <div  <?php

        if ( is_array( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {
                echo esc_html( $key ) . "='" . esc_attr( $val ) . "' ";
            }

        }

        ?>>
            <lable><?php echo esc_html( $lable ); ?> </lable>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <select name="<?php echo esc_html( $this->prefix . $name ); ?>">
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
