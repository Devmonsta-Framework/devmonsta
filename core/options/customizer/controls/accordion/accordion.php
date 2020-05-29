<?php
namespace Devmonsta\Options\Customizer\Controls\Accordion;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class Accordion extends  \WP_Customize_Control  {
    /**
     * The type of control being rendered
     */
    public $type = 'single_accordion';
    /**
     * Enqueue our scripts and styles
     */
    public function enqueue() {
        wp_enqueue_script( 'devmonsta-customizer-js', DM_CORE . '/options/customizer/libs/assets/js/customizer.js', array( 'jquery' ), null, true );
        wp_enqueue_style( 'devmonsta-customizer-css',  DM_CORE . '/options/customizer/libs/assets/css/customizer.css', [], null, 'all' );
    }
    /**
     * Render the control in the customizer
     */
    public function render_content() {
        $allowed_html = array(
            'a' => array(
                'href' => array(),
                'title' => array(),
                'class' => array(),
                'target' => array(),
            ),
            'br' => array(),
            'em' => array(),
            'strong' => array(),
            'i' => array(
                'class' => array()
            ),
        );
    ?>
        <div class="single-accordion-custom-control">
            <div class="single-accordion-toggle"><?php echo esc_html( $this->label ); ?><span class="accordion-icon-toggle dashicons dashicons-plus"></span></div>
            <div class="single-accordion customize-control-description">
                <?php
                    if ( is_array( $this->description ) ) {
                        echo '<ul class="single-accordion-description">';
                        foreach ( $this->description as $key => $value ) {
                            echo '<li>' . $key . wp_kses( $value, $allowed_html ) . '</li>';
                        }
                        echo '</ul>';
                    }
                    else {
                        echo wp_kses( $this->description, $allowed_html );
                    }
              ?>
            </div>
        </div>
    <?php
    }
}