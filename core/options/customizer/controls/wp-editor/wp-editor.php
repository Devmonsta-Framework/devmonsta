<?php
namespace Devmonsta\Options\Customizer\Controls\WpEditor;

use Devmonsta\Options\Customizer\Structure;

class WpEditor extends Structure {

    public $label, $name, $desc, $value, $default_value,
            $default_attributes, $editor_height, $editor_wpautop;

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
        $this->statuses = ['' =>esc_html__( 'Default' )];
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
        $this->default_value = isset( $args[0]['value'] ) ? $args[0]['value'] : "";

        $this->editor_height = isset($args[0]["editor_height"]) && is_numeric($args[0]["editor_height"]) ? $args[0]["editor_height"] : 200;
        $this->editor_wpautop = isset($args[0]["wpautop"]) ? (bool) $args[0]['wpautop'] : false;

        //generate attributes dynamically for parent tag
        if(isset( $args[0] )){
        $this->default_attributes = $this->prepare_default_attributes( $args[0] );
        }
    }

    function editor_customizer_script() {
        // wp_enqueue_script( 'wp-editor-customizer', DEVMONSTA_CORE . 'options/customizer/controls/wp-editor/assets/js/script.js', array( 'jquery' ), rand(), true );
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {
        add_action( 'customize_controls_enqueue_scripts', [$this, 'editor_customizer_script'] );
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
            <li <?php echo devm_render_markup( $this->default_attributes ); ?>>
                <div class="devm-option-column left">
                    <label class="devm-option-label"><?php echo esc_html( $this->label ); ?> </label>
                </div>

                <div class="devm-option-column right wp-editor-parent">

                    <input class="wp-editor-area" type="hidden" <?php $this->link(); ?> value="<?php echo esc_textarea( $this->value() ); ?>">
                        <?php
                        $content = !empty( $this->value() ) && !is_null( $this->value() ) ? $this->value() : $this->default_value;
                        $editor_id = $this->name;
                        $settings = array(
                            'textarea_name' => $this->name,
                            "wpautop" => $this->editor_wpautop,
                            "editor_height" => $this->editor_height,
                            "tinymce" => false
                        );
                        $this->filter_editor_setting_link();
                        wp_editor($content, $this->id, $settings );

                        do_action('admin_footer');
                        do_action('admin_print_footer_scripts');
                        ?>
                    <p class="devm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
                </div>
            </li>
    <?php
    }

    private function filter_editor_setting_link() {
        add_filter( 'the_editor', function( $output ) { return preg_replace( '/<textarea/', '<textarea ' . $this->get_link(), $output, 1 ); } );
    }

}
