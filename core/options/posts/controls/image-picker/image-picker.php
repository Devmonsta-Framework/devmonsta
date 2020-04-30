<?php

namespace Devmonsta\Options\Posts\Controls\ImagePicker;

use Devmonsta\Options\Posts\Structure;

class ImagePicker extends Structure {

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

        // if ( !empty( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) )
        //     && !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ) {
        //     $this->value = maybe_unserialize( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) );
        // }
        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $label   = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $help    = isset( $this->content['help'] ) ? $this->content['help'] : '';
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
            <lable><?php echo esc_html( $label ); ?> </lable>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>         
            <?php
                foreach ($choices as $key => $item) {
                   if ($key == 'value-1') {
                       ?>
                        <input type="checkbox" name=""/>
                        <img src="<?php echo esc_attr($item) ;?>" alt="not found">
                       <?php
                   }
                   elseif ($key == 'value-2') {
                       if (is_array($item)) {
                            ?>
                                <input type="checkbox" name=""/>
                                <img src="<?php echo esc_attr( $item['small'] ) ;?>" alt="not found"/>
                                <input type="checkbox" name=""/>
                                <img src="<?php echo esc_attr( $item['large'] ) ;?>" alt="not found"/>
                            <?php
                       }
                   }
                }
                echo '<div>'.esc_html( $help ).'</div>';
            ?>
        </div>
    <?php
}

}
