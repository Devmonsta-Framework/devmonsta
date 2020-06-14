<?php
namespace Devmonsta\Options\Customizer\Controls\Dimensions;

use Devmonsta\Options\Customizer\Structure;

class Dimensions extends Structure {

    public $label, $name, $desc, $default_value, $value, $default_attributes;
    public $type = 'dimensions';
    public $statuses;

    public function __construct( $manager, $id, $args = [] ) {

        $this->prepare_values( $id, $args );
        $this->statuses = ['' => __( 'Default' )];
        parent::__construct( $manager, $id, $args );
    }

    public function prepare_values( $id, $args = [] ) {
        $this->label         = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name          = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc          = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
        $this->default_value = isset( $args[0]['value'] ) && is_array( $args[0]['value'] ) ? $args[0]['value'] : [];

        //generate attributes dynamically for parent tag
        $this->default_attributes = $this->prepare_default_attributes( $args[0] );
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {
        wp_enqueue_style('element-ui', 'https://unpkg.com/element-ui/lib/theme-chalk/index.css', [], null, '');
        wp_enqueue_script( 'dm-dimensions-components', DM_CORE . 'options/posts/controls/dimensions/assets/js/script.js', ['jquery'], time(), true );
    }


    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? (array) json_decode($this->value()) : $this->default_value;
        $this->render_content();
    }

    public function render_content() {
        $savedData = $this->value;
        // var_dump( $savedData );
        ?>
            <li <?php echo dm_render_markup( $this->default_attributes ); ?>>
            <style>
                .dm-option {
                    clear: both;
                }
            </style>
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>
            <div class="dm-option-column right dm-vue-app active-script">
                <dm-dimensions
                    dimension="<?php echo isset( $savedData['isLinked'] ) ? esc_attr( $savedData['isLinked'] ) : 'false'; ?>"  
                    linked-name="<?php echo esc_attr( $this->name ); ?>[isLinked]"
                    name="<?php echo esc_attr( $this->name ); ?>"
                >
                    <dm-dimensions-item
                        name="<?php echo esc_attr( $this->name ); ?>[top]"
                        class="dm-ctrl"
                        value="<?php echo isset( $savedData['top'] ) && is_numeric( $savedData['top'] ) ? esc_html( intval( $savedData['top'] ) ) : 0; ?>"
                        label="top"
                    ></dm-dimensions-item>

                    <dm-dimensions-item
                        
                        name="<?php echo esc_attr( $this->name ); ?>[right]"
                        class="dm-ctrl"
                        value="<?php echo isset( $savedData['right'] ) && is_numeric( $savedData['right'] ) ? esc_html( intval( $savedData['right'] ) ) : 0; ?>"
                        label="right"
                    ></dm-dimensions-item>

                    <dm-dimensions-item
                        
                        name="<?php echo esc_attr( $this->name ); ?>[bottom]"
                        class="dm-ctrl"
                        value="<?php echo isset( $savedData['bottom'] ) && is_numeric( $savedData['bottom'] ) ? esc_html( intval( $savedData['bottom'] ) ) : 0; ?>"
                        label="bottom"
                    ></dm-dimensions-item>

                    <dm-dimensions-item
                        
                        name="<?php echo esc_attr( $this->name ); ?>[left]"
                        class="dm-ctrl"
                        value="<?php echo isset( $savedData['left'] ) && is_numeric( $savedData['left'] ) ? esc_html( intval( $savedData['left'] ) ) : 0; ?>"
                        label="left"
                    ></dm-dimensions-item>
                </dm-dimensions>
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>
        <?php
    }
    
}
