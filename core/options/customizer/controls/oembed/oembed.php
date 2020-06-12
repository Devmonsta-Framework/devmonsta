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
        $this->data_preview  = isset( $args[0]['preview'] ) && is_array( $args[0]['preview'] ) ? json_encode( $args[0]['preview'] ) : "";
        $this->default_value = isset( $args[0]['value'] ) ? $args[0]['value'] : "";

        //generate attributes dynamically for parent tag
        $this->default_attributes = $this->prepare_default_attributes( $args[0] );
    }

    /**
     * @internal
     */
    public function enqueue(  ) {
        wp_register_script( 'dm-oembed', DM_CORE . 'options/posts/controls/oembed/assets/js/script.js', ['underscore', 'wp-util'], time(), true );
        wp_localize_script( 'dm-oembed', 'object', ['ajaxurl' => admin_url( 'admin-ajax.php' )] );
        wp_enqueue_script( 'dm-oembed' );
        add_action( 'wp_ajax_get_oembed_response', [$this, '_action_get_oembed_response'] );
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
        $wrapper_attr['data-nonce']   = wp_create_nonce( '_action_get_oembed_response' );
        $wrapper_attr['data-preview'] = $this->data_preview;
        ?>
        <li <?php echo dm_render_markup( $this->default_attributes ); ?>>
                <div class="dm-option-column left">
                    <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
                </div>
                <div class="dm-option-column right dm-oembed-input">
                    <input <?php echo dm_attr_to_html( $wrapper_attr ) ?> <?php $this->link(); ?>
                            type="url" name="<?php echo esc_attr( $this->name ); ?>"
                            value="<?php echo esc_html( $this->value ); ?>"
                            class="dm-option-input dm-ctrl dm-oembed-url-input"/>
                    <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
                    <div class="dm-oembed-preview"></div>
                </div>
            </li>
    <?php
    }

    /**
     * Calls wp_oembed built-in function to get data from an url
     *
     * @return void
     */
    public static function _action_get_oembed_response() {

        if ( wp_verify_nonce( \DM_Request::POST( '_nonce' ), '_action_get_oembed_response' ) ) {
    
            require_once DM_DIR . '/core/helpers/class-dm-request.php';
            
            $url = \DM_Request::POST( 'url' );
    
            $width = \DM_Request::POST( 'preview/width' );
    
            $height = \DM_Request::POST( 'preview/height' );
    
            $keep_ratio = ( \DM_Request::POST( 'preview/keep_ratio' ) === 'true' );
    
            $iframe = empty( $keep_ratio ) ?
    
            dm_oembed_get( $url, compact( 'width', 'height' ) ) :
    
            wp_oembed_get( $url, compact( 'width', 'height' ) );
    
            echo dm_render_markup($iframe) ;
            die();
    
        } else {
            echo esc_html_e('Invalid nonce', 'devmonsta');
            die();
        }
    }
}