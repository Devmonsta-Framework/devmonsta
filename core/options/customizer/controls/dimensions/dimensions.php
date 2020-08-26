<?php
namespace Devmonsta\Options\Customizer\Controls\Dimensions;

use Devmonsta\Options\Customizer\Structure;

class Dimensions extends Structure {

    public $label, $name, $desc, $default_value, $value, $default_attributes;
    public $type = 'dimensions';
    public $statuses;

    public function __construct( $manager, $id, $args = [] ) {

        $this->prepare_values( $id, $args );
        $this->statuses = ['' =>esc_html__( 'Default' )];
        parent::__construct( $manager, $id, $args );
    }

    public function prepare_values( $id, $args = [] ) {
        $this->label         = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name          = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc          = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
        $this->default_value = isset( $args[0]['value'] ) && is_array( $args[0]['value'] ) ? $args[0]['value'] : [];

        //generate attributes dynamically for parent tag
        if(isset( $args[0] )){
            $this->default_attributes = $this->prepare_default_attributes( $args[0] );
        }
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {
        // wp_enqueue_style('element-ui', DEVMONSTA_CORE . 'options/posts/controls/dimensions/assets/css/unpkg.css', [], null, '');
        // wp_enqueue_script( 'devm-dimensions-components', DEVMONSTA_CORE . 'options/posts/controls/dimensions/assets/js/script.js', ['jquery'], time(), true );
    }


    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? (array) json_decode($this->value()) : $this->default_value;
        $this->render_content();
    }

    public function render_content() {
        $savedData = $this->value;
        // var_dump( $savedData );
        ?>
            <li <?php echo devm_render_markup( $this->default_attributes ); ?>>
            <style>
                .devm-option {
                    clear: both;
                }
            </style>
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>
            <div class="devm-option-column right devm-vue-app active-script">
                <devm-dimensions
                    :dimension="<?php echo ( isset( $savedData['isLinked'] ) && true ==$savedData['isLinked'] ) ? "true" : "false"; ?>"
                    linked-name="<?php echo esc_attr( $this->name ); ?>[isLinked]"
                    name="<?php echo esc_attr( $this->name ); ?>"
                >
                    <devm-dimensions-item
                        name="<?php echo esc_attr( $this->name ); ?>[top]"
                        class="devm-ctrl"
                        value="<?php echo isset( $savedData['top'] ) && is_numeric( $savedData['top'] ) ? esc_html( intval( $savedData['top'] ) ) : 0; ?>"
                        label="top"
                    ></devm-dimensions-item>

                    <devm-dimensions-item

                        name="<?php echo esc_attr( $this->name ); ?>[right]"
                        class="devm-ctrl"
                        value="<?php echo isset( $savedData['right'] ) && is_numeric( $savedData['right'] ) ? esc_html( intval( $savedData['right'] ) ) : 0; ?>"
                        label="right"
                    ></devm-dimensions-item>

                    <devm-dimensions-item

                        name="<?php echo esc_attr( $this->name ); ?>[bottom]"
                        class="devm-ctrl"
                        value="<?php echo isset( $savedData['bottom'] ) && is_numeric( $savedData['bottom'] ) ? esc_html( intval( $savedData['bottom'] ) ) : 0; ?>"
                        label="bottom"
                    ></devm-dimensions-item>

                    <devm-dimensions-item

                        name="<?php echo esc_attr( $this->name ); ?>[left]"
                        class="devm-ctrl"
                        value="<?php echo isset( $savedData['left'] ) && is_numeric( $savedData['left'] ) ? esc_html( intval( $savedData['left'] ) ) : 0; ?>"
                        label="left"
                    ></devm-dimensions-item>
                </devm-dimensions>
                <p class="devm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>
        <?php
    }

}
