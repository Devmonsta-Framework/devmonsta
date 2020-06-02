<?php
namespace Devmonsta\Options\Customizer\Controls\Checkboxes;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class Checkboxes extends \WP_Customize_Control {

    public $label, $name, $desc, $default_value, $value, $choices, $isInline;

    /**
     * @access public
     * @var    string
     */
    public $type = 'checkboxes';

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
        $this->isInline      = ( $args[0]['inline'] ) ? "inline" : "list";
        $this->default_value = isset( $args[0]['value'] ) ? $args[0]['value'] : [];
        $this->choices       = isset( $args[0]['choices'] ) && is_array( $args[0]['choices'] ) ? $args[0]['choices'] : [];
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
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? maybe_unserialize( $this->value() ) : $this->default_value;
        $this->render_content();
    }

    public function render_content() {
        ?>
            <div>
                <div class="dm-option-column left">
                    <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
                </div>

                <div class="dm-option-column right <?php echo ( $this->isInline ) ? esc_attr( $this->isInline ) : ""; ?>">
                    <?php

                        if ( is_array( $this->choices ) && !empty( $this->choices ) ) {

                            foreach ( $this->choices as $id => $element ) {

                                if ( is_array( $this->value ) && in_array( $id, $this->value ) ) {
                                    $checked = 'checked="checked"';
                                } else {
                                    $checked = null;
                                }

                                ?>
                                    <label class="dm-option-label-list">
                                        <input class="dm-ctrl" type="checkbox" name="<?php echo esc_attr( $this->name ); ?>[]"
                                            value="<?php echo esc_attr( $id ); ?>" <?php echo esc_attr( $checked ); ?> />
                                            <?php echo esc_html( $element ); ?>
                                    </label>
                   <?php
                            }

                        }

                    ?>
                    <input class="dm-ctrl" type="text" value="default" name="<?php echo esc_attr( $this->name ); ?>[]" style="display: none">
                    <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
                </div>
            </div>
    <?php
    }

}
