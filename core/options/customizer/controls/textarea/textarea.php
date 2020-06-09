<?php
namespace Devmonsta\Options\Customizer\Controls\Textarea;

use Devmonsta\Options\Customizer\Structure;

class Textarea extends Structure {

    public $label, $name, $desc, $default_value, $value, $default_attributes;

    /**
     * @access public
     * @var    string
     */
    public $type = 'textarea';

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
        
        //generate attributes dynamically for parent tag
        $this->default_attributes = $this->prepare_default_attributes( $args[0] );
    }

    /**
     * Internal
     *
     * @return void
     */
    public function enqueue() {
    }

    /**
     * @internal
     */
    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? $this->value() : $this->default_value;
        // var_dump($this->value);
        $this->render_content();
    }

    public function render_content() {
        ?>
        <li <?php echo dm_render_markup( $this->default_attributes ); ?>>
            <div class="dm-option-column left">
                <label class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>

            <div class="dm-option-column right">
                <textarea class="large-text" cols="20" rows="5" <?php $this->link();?>>
                    <?php echo esc_textarea( $this->value() ); ?>
                </textarea>
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>
		<?php
    }

}
