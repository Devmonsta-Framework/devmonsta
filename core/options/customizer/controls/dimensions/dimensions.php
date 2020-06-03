<?php
namespace Devmonsta\Options\Customizer\Controls\Dimensions;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class Dimensions extends \WP_Customize_Control {

    public $label, $name, $desc, $default_value, $value;
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
        $this->default_value = isset( $args[0]['value'] ) ? $args[0]['value'] : [];
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {
		wp_enqueue_style('element-ui', 'https://unpkg.com/element-ui/lib/theme-chalk/index.css', [], null, '');
        
        
        wp_enqueue_style( 'dm-dimensions-css', DM_CORE . 'options/customizer/controls/dimensions/assets/css/style.css', [], time(), true );

        
    }


    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? maybe_unserialize( $this->value() ) : $this->default_value;
        $this->render_content();
    }

    public function render_content() {
        ?>
        <li class="dm-option dm-box">
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="dm-option-column right">
                <dm-dimensions
                    :dimension="<?php echo isset( $this->value["isLinked"] ) ? esc_attr( $this->value["isLinked"] ) : 'false'; ?>" <?php $this->link();?> linked-name="<?php echo esc_attr( $this->name ); ?>[isLinked]"
                >
                    <dm-dimensions-item
                        <?php $this->link();?>
                        name="<?php echo esc_attr( $this->name ); ?>[top]"
                        class="dm-ctrl"
                        value="<?php echo isset( $this->value["top"] ) ? esc_html( intval( $this->value["top"] ) ) : 0; ?>"
                        label="top"
                    ></dm-dimensions-item>

                    <dm-dimensions-item
                        <?php $this->link();?>
                        name="<?php echo esc_attr( $this->name ); ?>[right]"
                        class="dm-ctrl"
                        value="<?php echo isset( $this->value["right"] ) ? esc_html( intval( $this->value["right"] ) ) : 0; ?>"
                        label="right"
                    ></dm-dimensions-item>

                    <dm-dimensions-item
                        <?php $this->link();?>
                        name="<?php echo esc_attr( $this->name ); ?>[bottom]"
                        class="dm-ctrl"
                        value="<?php echo isset( $this->value["bottom"] ) ? esc_html( intval( $this->value["bottom"] ) ) : 0; ?>"
                        label="bottom"
                    ></dm-dimensions-item>

                    <dm-dimensions-item
                        <?php $this->link();?>
                        name="<?php echo esc_attr( $this->name ); ?>[left]"
                        class="dm-ctrl"
                        value="<?php echo isset( $this->value["left"] ) ? esc_html( intval( $this->value["left"] ) ) : 0; ?>"
                        label="left"
                    ></dm-dimensions-item>
                </dm-dimensions>
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>

		
    
        <script src='//unpkg.com/vue/dist/vue.js'></script>
        <script src='<?php echo plugin_dir_url(__FILE__); ?>assets/js/script.js'></script>
        <script>
            let elements = document.querySelectorAll('.dm-box');
            elements.forEach(function (item) {
                new Vue({
                    el: item
                });
            });

            let taxonomyEl = document.getElementById('addtag');
            if (taxonomyEl) {
                new Vue({
                    el: taxonomyEl
                });
            }
        </script>

        <?php
    }
    
}
