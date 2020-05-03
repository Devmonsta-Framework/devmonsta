<?php

namespace Devmonsta\Options\Posts\Controls\Multiselect;

use Devmonsta\Options\Posts\Structure;

class Multiselect extends Structure {

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
        wp_enqueue_style('select2-css', plugins_url( 'multiselect/assets/css/select2.min.css', dirname( __FILE__ ) ));
        wp_enqueue_script( 'select2-js', plugins_url( 'multiselect/assets/js/select2.min.js', dirname( __FILE__ ) ) );
        wp_enqueue_script( 'dm-multiselect-js', plugins_url( 'multiselect/assets/js/script.js', dirname( __FILE__ ) ), ['jquery', 'select2-js'], time(), true );

    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;

        if ( !empty( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
            && !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) {
            $this->value = maybe_unserialize( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) );
        }
        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label   = isset( $this->content['label'] ) ? $this->content['label'] : '';
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
            <label><?php echo esc_html( $label ); ?> </label>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <select id="dm_multi_select" multiple="multiple" name="<?php echo esc_attr( $this->prefix . $name ); ?>[]">
        <?php

        if ( isset( $choices ) ) {
            foreach ( $choices as $key => $val ) {
                if ( is_array( $this->value ) && in_array( $key, $this->value ) ) {
                    $selected = 'selected';
                } else {
                    $selected = null;
                }
        ?>
                    <option value="<?php echo esc_attr( $key ); ?>"
                            <?php echo esc_html( $selected ); ?>>
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
