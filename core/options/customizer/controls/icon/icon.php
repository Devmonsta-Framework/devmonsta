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
        $this->statuses = ['' => __( 'Default' )];
        parent::__construct( $manager, $id, $args );
    }

    public function prepare_values( $id, $args = [] ) {
        $this->label         = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name          = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc          = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";

        //generate attributes dynamically for parent tag
        $this->default_attributes = $this->prepare_default_attributes( $args[0], "dm-vue-app" );
    }


    /**
     * @internal
     */
    public function enqueue(  ) {
        wp_enqueue_style( 'dm-fontawesome-css', DM_CORE . 'options/posts/controls/icon/assets/css/font-awesome.min.css' );
        wp_enqueue_style( 'dm-main-css', DM_CORE . 'options/posts/controls/icon/assets/css/main.css' );
        wp_enqueue_script( 'dm-icon-components', DM_CORE . 'options/posts/controls/icon/assets/js/script.js', ['jquery'], time(), true );
        wp_enqueue_script( 'dm-asicon', DM_CORE . 'options/posts/controls/icon/assets/js/script.js', ['jquery'], time(), true );
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
        $icon_value = !empty( $this->value() ) && is_array( $this->value() ) ? explode(',', $this->value()) : ["fas fa-angle-right", "dm-font-awesome"];
        $this->icon_name = $icon_value[0];
        $this->icon_type = $icon_value[1];
        $savedData = json_decode($this->value());
        ?>
        <li <?php echo dm_render_markup( $this->default_attributes ); ?>>
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="dm-option-column right dm-vue-app active-script">
                <dm-icon-picker 
                        name='<?php echo esc_attr( $this->name ); ?>'
                        class="dm-ctrl"
                        icon_list='<?php echo dm_render_markup($iconEncoded); ?>'
                        default_icon_type='<?php echo isset( $savedData->iconType ) ? esc_attr( $savedData->iconType ) : "dm-font-awesome"; ?>'
                        default_icon='<?php echo isset( $savedData->icon ) ? esc_attr( $savedData->icon ) : "fas fa-angle-right"; ?>'
                    ></dm-icon-picker>

                    <input type="hidden" <?php $this->link();?>  value="<?php echo esc_attr( implode( ',', $icon_value ) ); ?>" >
                    
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>

    <?php
    }

}
