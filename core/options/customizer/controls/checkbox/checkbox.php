<?php
namespace Devmonsta\Options\Customizer\Controls\Checkbox;

use Devmonsta\Options\Customizer\Structure;

class Checkbox extends Structure {
    public $label, $name, $desc, $default_value, $value, $text, $default_attributes;

    /**
     * @access public
     * @var    string
     */
    public $type = 'checkbox';

    public $statuses;

    public function __construct( $manager, $id, $args = [] ) {

        $this->prepare_values( $id, $args );
        $this->statuses = ['' =>esc_html__( 'Default' )];
        parent::__construct( $manager, $id, $args );
    }

    private function prepare_values( $id, $args = [] ) {
        // var_dump($args[0]);
        $this->label         = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name          = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc          = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
        $this->text          = isset( $args[0]['text'] ) ? $args[0]['text'] : "";
        $this->default_value = isset( $args[0]['value'] ) ? $args[0]['value'] : "";

        //generate attributes dynamically for parent tag
        if(isset( $args[0] )){
            $this->default_attributes = $this->prepare_default_attributes( $args[0] );
        }
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {

    }

    /**
     * @internal
     */
    public function render() {
        $this->value   = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? $this->value() : $this->default_value;
        
        $this->render_content();
    }

    public function render_content() {
        $is_checked = ( $this->value == 'true' ) ? 'checked' : '';
        ?>
        <li <?php echo devm_render_markup( $this->default_attributes ); ?>>
            <div class="devm-option-column left">
                <label class="devm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="devm-option-column right">
                <input <?php $this->link();?> type="text" value="false" name="<?php echo esc_attr( $this->name ); ?>" style="display: none">

                <label class="devm-option-label-list">
                    <input <?php $this->link();?> type="checkbox" name="<?php echo esc_attr( $this->name ); ?>" value="true" <?php echo esc_attr( $is_checked ); ?> class="devm-ctrl ">
                    <?php echo esc_html( $this->text ); ?>
                </label>
                <p class="devm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>

        <?php
    }


}
