<?php
namespace Devmonsta\Options\Customizer\Controls\ImagePicker;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}
class ImagePicker extends \WP_Customize_Control {

    public $label, $name, $desc, $default_value, $value, $choices;

    /**
     * @access public
     * @var    string
     */
    public $type = 'image-picker';

    public $statuses;

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
        $this->default_value = isset( $args[0]['value'] ) ? $args[0]['value'] : "";
        $this->choices       = isset( $args[0]['choices'] ) && is_array( $args[0]['choices'] ) ? $args[0]['choices'] : [];
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {

        // js
        wp_enqueue_script( 'dm-image-picker-js', plugins_url( 'image-picker/assets/js/image-picker.js', dirname( __FILE__ ) ), ['jquery'], time(), true );
        // css
        wp_enqueue_style( 'dm-image-picker-css', plugins_url( 'image-picker/assets/css/image-picker.css', dirname( __FILE__ ) ) );
        // var_dump($this->name);
        wp_localize_script( 'dm-image-picker-js', 'settings_id', $this->name );
    }

    
    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? $this->value() : $this->default_value;
        $this->render_content();
    }

    public function render_content() {
        ?>
        <li  class="dm-option">
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="dm-option-column right full-width">
                <div class="thumbnails dm-option-image_picker_selector">
                        <input class="dm-ctrl dm-option-image-picker-input" type="hidden" name="<?php echo esc_attr( $this->name ); ?>" value="<?php echo esc_attr( $this->value ); ?>">
                        <ul>
                            <?php
                                if ( is_array( $this->choices ) && isset( $this->choices ) ) {

                                    foreach ( $this->choices as $item_key => $item ) {
                                        if(is_array($item) && isset($item)){
                                            $selected    = ( $item_key == $this->value ) ? 'selected' : '';
                                            $small_image = isset( $item['small'] ) ? $item['small'] : '';
                                            $large_image = isset( $item['large'] ) ? $item['large'] : '';
                                            ?>
                                            <li data-image_name='<?php echo esc_attr( $item_key ); ?>' class='<?php echo esc_attr( $selected ); ?>'>

                                                <label>
                                                    <input <?php $this->link(); ?> id="<?php echo esc_attr( $this->name ) . $item_key; ?>" class="dm-ctrl dm-option-image-picker-input" type="radio" name="<?php echo esc_attr( $this->name ); ?>" value="<?php echo esc_attr( $item_key ); ?>">

                                                    <div class="dm-img-list" for="<?php echo esc_attr( $this->name ) . $item_key; ?>">
                                                        <?php if ( !empty( $large_image ) ): ?>
                                                        <div class="dm-img-picker-preview">
                                                            <img src="<?php echo esc_attr( $large_image ); ?>" />
                                                        </div>
                                                        <?php endif;?>
                                                        <div class="thumbnail">
                                                            <img src="<?php echo esc_attr( $small_image ); ?>" />
                                                        </div>
                                                    </div>
                                                </label>

                                            </li>
                                            <?php
                                        }
                                    }

                                }

                            ?>
                        </ul>
                    </div>
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>
        <?php
    }

}
