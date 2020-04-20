<?php

namespace Devmonsta\Options\Posts\Controls\Hidden;

use Devmonsta\Options\Posts\Structure;

class Hidden extends Structure {

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
        wp_enqueue_script( 'dm-hidden-js', plugins_url( 'hidden/assets/js/script.js', dirname( __FILE__ ) ) );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;

        $this->value = ( "" != get_post_meta( $post->ID, $this->prefix . $content['name'], true ) 
                            && !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) ?
                            get_post_meta( $post->ID, $this->prefix . $content['name'], true )
                            : $content['value'];
        $this->output();
    }

    /**
     * @internal
     */
    public function output() {

        $name  = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $attrs = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        ?>
        <div  <?php

        if ( is_array( $attrs ) ) {

            foreach ( $attrs as $key => $val ) {
                echo esc_html( $key ) . "='" . esc_attr( $val ) . "' ";
            }

        }

        ?>>
            <input style="display: none" type="text" name="<?php echo esc_html( $this->prefix . $name ); ?>" value="<?php echo esc_html( $this->value ); ?>" >
        </div>
    <?php
}

}
