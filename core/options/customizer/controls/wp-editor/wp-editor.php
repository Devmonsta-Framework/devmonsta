<?php
namespace Devmonsta\Options\Customizer\Controls\WpEditor;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class WpEditor extends \WP_Customize_Control {

    public $label, $name, $desc, $value;

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
        $this->label         = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name          = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc          = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
    }

    function editor_customizer_script() {
        wp_enqueue_script( 'wp-editor-customizer', DM_CORE . 'options/customizer/controls/wp-editor/assets/js/script.js', array( 'jquery' ), rand(), true );
    }
    
    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {
        add_action( 'customize_controls_enqueue_scripts', [$this, 'editor_customizer_script'] );
         
        // wp_enqueue_script( 'dm-customizer-wpeditor-handle', DM_CORE . 'options/customizer/controls/wp-editor/assets/js/script.js', ['jquery'], false, true );
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
        ?>
            <li class="dm-option">
                <div class="dm-option-column left">
                    <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
                </div>

                <div class="dm-option-column right wp-editor-parent">
                    
                    <input class="wp-editor-area" type="hidden" <?php $this->link(); ?> value="<?php echo esc_textarea( $this->value() ); ?>">
                        <?php
                        $content = $this->value();
                        $editor_id = $this->name;
                        $settings = array(
                            'textarea_name' => $this->name,
                            'media_buttons' => true,
                            'drag_drop_upload' => false,
                            'teeny' => true,
                            'quicktags' => true,
                            'textarea_rows' => 5,
                        );
                        $this->filter_editor_setting_link();
                        wp_editor($this->value(), $this->id, $settings );
                        
                        do_action('admin_footer');
                        do_action('admin_print_footer_scripts');
                        ?>
                    <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
                </div>
            </li>
    <?php
    }

    private function filter_editor_setting_link() {
        add_filter( 'the_editor', function( $output ) { return preg_replace( '/<textarea/', '<textarea ' . $this->get_link(), $output, 1 ); } );
    }

}
