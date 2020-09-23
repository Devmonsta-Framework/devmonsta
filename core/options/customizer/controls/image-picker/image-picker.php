<?php
namespace Devmonsta\Options\Customizer\Controls\ImagePicker;

use Devmonsta\Options\Customizer\Structure;

class ImagePicker extends Structure {

    public $label, $name, $desc, $default_value, $value, $choices, $default_attributes;

    /**
     * @access public
     * @var    string
     */
    public $type = 'image-picker';

    public $statuses;

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
        $this->choices       = isset( $args[0]['choices'] ) && is_array( $args[0]['choices'] ) ? $args[0]['choices'] : [];

        //generate attributes dynamically for parent tag
        if(isset( $args[0] )){
        $this->default_attributes = $this->prepare_default_attributes( $args[0] );
        }
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {
        // css
        // wp_enqueue_style( 'devm-image-picker-css', DEVMONSTA_CORE . 'options/posts/controls/image-picker/assets/css/image-picker.css' );
    }


    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? $this->value() : $this->default_value;
        $this->render_content();
    }

    public function render_content() {
        ?>
        <li <?php echo devm_render_markup( $this->default_attributes ); ?>>
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="devm-option-column right full-width">
                <div class="thumbnails devm-option-image_picker_selector">
                        <input class="devm-ctrl devm-option-image-picker-input" type="hidden" name="<?php echo esc_attr( $this->name ); ?>" value="<?php echo esc_attr( $this->value ); ?>">
                        <ul>
                            <?php
                                if ( is_array( $this->choices ) && isset( $this->choices ) ) {

                                    foreach ( $this->choices as $item_key => $item ) {
                                        if(is_array($item) && isset($item)){
                                            $selected    = ( $item_key == $this->value ) ? 'checked' : '';
                                            $small_image = isset( $item['small'] ) ? $item['small'] : '';
                                            $large_image = isset( $item['large'] ) ? $item['large'] : '';
                                            if(isset($small_image)){
                                                ?>
                                                <li data-image_name='<?php echo esc_attr( $item_key ); ?>'>
                                                    <label>
                                                        <input <?php $this->link(); ?> <?php echo esc_attr( $selected ); ?> id="<?php echo esc_attr( $this->name ) . $item_key; ?>" class="devm-ctrl devm-option-image-picker-input" type="radio" name="<?php echo esc_attr( $this->name ); ?>" value="<?php echo esc_attr( $item_key ); ?>">
                                                        <div class="devm-img-list" for="<?php echo esc_attr( $this->name ) . $item_key; ?>">
                                                            <?php if ( !empty( $large_image ) ): ?>
                                                            <div class="devm-img-picker-preview">
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
                                }
                            ?>
                        </ul>
                    </div>
                <p class="devm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>
        <?php
    }

}
