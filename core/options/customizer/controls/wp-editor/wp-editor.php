<?php
namespace Devmonsta\Options\Customizer\Controls\WpEditor;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class WpEditor extends \WP_Customize_Control {

    public $label, $name, $desc, $value, $settings;

    /**
     * The type of customize control being rendered.
     *
     * @since  1.0.0
     * @access public
     * @var    string
     */
    public $type = 'wp-editor';

    public $statuses;

    /**
     * Constructor of this control. Must call parent constructor
     *
     * @since  1.0.0
     * @access public
     * @var    string
     */
    public function __construct( $manager, $id, $args = [] ) {
        $this->prepare_values( $id, $args );
        $this->statuses = ['' => __( 'Default' )];
        parent::__construct( $manager, $id, $args );
    }

    /**
     * Prepare default values passed from theme
     *
     * @param [type] $id
     * @param array $args
     * @return void
     */
    private function prepare_values( $id, $args = [] ) {
        $this->label                     = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name                      = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc                      = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
        $this->settings["wpautop"]       = ( isset( $args[0]['wpautop'] ) ) ? $args[0]['wpautop'] : false;
        $this->settings["editor_height"] = ( isset( $args[0]['editor_height'] ) ) ? (int) $args[0]['editor_height'] : 285;
        $this->settings["tinymce"]       = ( isset( $args[0]['editor_type'] ) && $args[0]['editor_type'] === false ) ? false : true;
    }

    /**
     * @internal
     */
    public function enqueue() {

    }

    /**
     * @internal
     */
    public function render() {
        $this->render_content();
    }

    /**
     * Generates markup for specific control
     * @internal
     */
    public function render_content() {
        $wrapper_attr['data-nonce']   = wp_create_nonce( '_action_get_oembed_response' );
        $wrapper_attr['data-preview'] = $this->data_preview;
        // var_dump("Hello");
        ?>
            <li class="dm-option">
                <div class="dm-option-column left">
                    <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
                </div>

                <div class="dm-option-column right">
                    <?php
                    wp_editor( $this->value(), $this->name, $this->settings );
                    $editor_html = ob_get_contents();
                    $editor_html .= "<p class='dm-option-desc'>" . esc_html( $this->desc ) . " </p>";
                    ob_end_clean();

                    echo dm_render_markup( $editor_html );
                    ?>
                </div>
            </li>
    <?php
}

}
