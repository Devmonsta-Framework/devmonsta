<?php
namespace Devmonsta\Options\Customizer\Controls\Icon;

use Devmonsta\Options\Customizer\Structure;

class Icon extends Structure {

    public $label, $name, $desc, $icon_type, $icon_name, $default_attributes;

    /**
     * @access public
     * @var    string
     */
    public $type = 'icon';

    public $statuses;

    public function __construct( $manager, $id, $args = [] ) {

        $this->prepare_values( $id, $args );
        $this->statuses = ['' =>esc_html__( 'Default' )];
        parent::__construct( $manager, $id, $args );
    }

    public function prepare_values( $id, $args = [] ) {
        $this->label                        = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name                         = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc                         = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
        $this->default_value['icon']        = isset( $args[0]['value']['icon'] ) ? $args[0]['value']['icon'] : "fab fa-500px";
        $this->default_value['iconType']    = isset( $args[0]['value']['type'] ) ? $args[0]['value']['type'] : "devm-font-awesome";
        
        //generate attributes dynamically for parent tag
        if(isset( $args[0] )){
        $this->default_attributes = $this->prepare_default_attributes( $args[0], "devm-vue-app" );
        }
    }


    /**
     * @internal
     */
    public function enqueue(  ) {
        // wp_enqueue_style( 'devm-fontawesome-css', DEVMONSTA_CORE . 'options/posts/controls/icon/assets/css/font-awesome.min.css' );
        // wp_enqueue_style( 'devm-main-css', DEVMONSTA_CORE . 'options/posts/controls/icon/assets/css/main.css' );
        // wp_enqueue_script( 'devm-icon-components', DEVMONSTA_CORE . 'options/posts/controls/icon/assets/js/script.js', ['jquery'], time(), true );
        // wp_enqueue_script( 'devm-asicon', DEVMONSTA_CORE . 'options/posts/controls/icon/assets/js/script.js', ['jquery'], time(), true );
    }

    /**
     * @internal
     */
    public function render() {
        $this->render_content();
    }

    public function render_content() {
        include 'icon-data.php';
        $iconEncoded = json_encode( $iconList );
        $savedData = !empty( $this->value() ) ? (array) json_decode($this->value()) : $this->default_value;
        ?>
        <li <?php echo devm_render_markup( $this->default_attributes ); ?>>
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="devm-option-column right devm-vue-app active-script">
                <devm-icon-picker
                        name='<?php echo esc_attr( $this->name ); ?>'
                        
                        icon_list='<?php echo devm_render_markup($iconEncoded); ?>'
                        default_icon_type='<?php echo isset( $savedData['iconType'] ) ? esc_attr( $savedData['iconType'] ) : ""; ?>'
                        default_icon='<?php echo isset( $savedData['icon'] ) ? esc_attr( $savedData['icon'] ) : ""; ?>'
                    ></devm-icon-picker>

                    <input type="hidden" <?php $this->link();?>  value="" >

                <p class="devm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>

    <?php
    }

}
