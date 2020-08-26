<?php
namespace Devmonsta\Options\Customizer\Controls\Oembed;

use Devmonsta\Options\Customizer\Structure;

class Oembed extends Structure {

    public $label, $name, $desc, $default_value, $value, $data_preview, $default_attributes;


    /**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'oembed';

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
        $this->data_preview  = isset( $args[0]['preview'] ) && is_array( $args[0]['preview'] ) ? json_encode( $args[0]['preview'] ) : "";
        $this->default_value = isset( $args[0]['value'] ) ? $args[0]['value'] : "";

        //generate attributes dynamically for parent tag
        if(isset( $args[0] )){
        $this->default_attributes = $this->prepare_default_attributes( $args[0], "devm-slider-holder" );
        }
    }

    /**
     * @internal
     */
    public function enqueue(  ) {
        // wp_register_script( 'devm-oembed', DEVMONSTA_CORE . 'options/posts/controls/oembed/assets/js/script.js', ['underscore', 'wp-util'], time(), true );
        // wp_localize_script( 'devm-oembed', 'object', ['ajaxurl' => admin_url( 'admin-ajax.php' )] );
        // wp_enqueue_script( 'devm-oembed' );
        // add_action( 'wp_ajax_get_oembed_response', [$this, 'action_get_oembed_response'] );
    }

    /**
     * @internal
     */
    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? $this->value() : $this->default_value;
        $this->render_content();
    }


    /**
     * Generates markup for specific control
     * @internal
     */
    public function render_content() {
        $wrapper_attr['data-nonce']   = wp_create_nonce( 'action_get_oembed_response' );
        $wrapper_attr['data-preview'] = $this->data_preview;
        $data = ['ajaxurl' => admin_url( 'admin-ajax.php' )];
        ?>
        <li <?php echo devm_render_markup( $this->default_attributes ); ?>>
                <div class="devm-option-column left">
                    <label class="devm-option-label"><?php echo esc_html( $this->label ); ?> </label>
                </div>
                <div class="devm-option-column right devm-oembed-input">
                    <input data-config='<?php echo json_encode($data); ?>' <?php echo devm_attr_to_html( $wrapper_attr ) ?> <?php $this->link(); ?>
                            type="url" name="<?php echo esc_attr( $this->name ); ?>"
                            data-value="<?php echo esc_html( $this->value ); ?>"
                            value="<?php echo esc_html( $this->value ); ?>"
                            class="devm-option-input devm-ctrl devm-oembed-url-input"
                            data-value="<?php echo esc_html( $this->value ); ?>"
                            />
                    <p class="devm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
                    <div class="devm-oembed-preview"></div>
                </div>
            </li>
    <?php
    }

    /**
     * Calls wp_oembed built-in function to get data from an url
     *
     * @return void
     */
    public static function action_get_oembed_response() {

        // Post data array from ajax request
        $post_array = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        //Check for valid nonce
        if ( wp_verify_nonce( $post_array[ '_nonce' ], 'action_get_oembed_response' ) ) {

            $url = $post_array[ 'url'];
            $width = $post_array['preview']['width'];
            $height = $post_array['preview']['height'];
            $keep_ratio = $post_array['preview']['height'] == true;
            $iframe = empty( $keep_ratio ) ? devm_oembed_get( $url, compact( 'width', 'height' ) ) : wp_oembed_get( $url, compact( 'width', 'height' ) );

            echo devm_render_markup( $iframe );
            die();

        } else {
            echo esc_html_e('Invalid nonce', 'devmonsta');
            die();
        }
    }
}