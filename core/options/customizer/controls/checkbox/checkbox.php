<?php
namespace Devmonsta\Options\Customizer\Controls\Checkbox;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class Checkbox extends \WP_Customize_Control {
    public $label, $name, $desc, $default_value, $value, $text;

    /**
     * @access public
     * @var    string
     */
    public $type = 'checkbox';

    public $statuses;

    public function __construct( $manager, $id, $args = [] ) {

        $this->prepare_values( $id, $args );
        $this->statuses = ['' => __( 'Default' )];
        parent::__construct( $manager, $id, $args );
    }

    private function prepare_values( $id, $args = [] ) {
        var_dump($args[0]);
        $this->label         = isset( $args[0]['label'] ) ? $args[0]['label'] : "";
        $this->name          = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->desc          = isset( $args[0]['desc'] ) ? $args[0]['desc'] : "";
        $this->text          = isset( $args[0]['text'] ) ? $args[0]['text'] : "";
        $this->default_value = isset( $args[0]['value'] ) ? $args[0]['value'] : "";
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
        <div>
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="dm-option-column right">
                <input <?php $this->link();?> type="text" value="false" name="<?php echo esc_attr( $this->name ); ?>" style="display: none">

                <label class="dm-option-label-list">
                    <input <?php $this->link();?> type="checkbox" name="<?php echo esc_attr( $this->name ); ?>" value="true" <?php echo esc_attr( $is_checked ); ?>>
                    <?php echo esc_html( $this->text ); ?>
                </label>
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </div>

        <?php
    }


}
