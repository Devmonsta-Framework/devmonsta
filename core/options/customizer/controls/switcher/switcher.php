<?php
namespace Devmonsta\Options\Customizer\Controls\Switcher;

use Devmonsta\Options\Customizer\Structure;

class Switcher extends Structure {

    public $label, $name, $desc, $default_value, 
            $value, $is_checked, $left_choice, $right_choice, 
            $left_key, $right_key, $default_attributes;

    /**
     * @access public
     * @var    string
     */
    public $type = 'switcher';

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
        $this->left_choice   = isset( $args[0]['left-choice'] ) && is_array( $args[0]['left-choice'] ) ? $args[0]['left-choice'] : [];
        $this->right_choice  = isset( $args[0]['right-choice'] ) && is_array( $args[0]['right-choice'] ) ? $args[0]['right-choice'] : [];
        $this->left_key      = $this->array_key_first( $this->left_choice );
        $this->right_key     = $this->array_key_first( $this->right_choice );

        //generate attributes dynamically for parent tag
        $this->default_attributes = $this->prepare_default_attributes( $args[0] );
    }

    /**
     * @internal
     */
    public function enqueue() {
        wp_enqueue_style( 'dm-switcher', DM_CORE . 'options/posts/controls/switcher/assets/css/dm-switcher.css' );
    }

    /**
     * @internal
     */
    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? $this->value() : $this->default_value;
        $this->is_checked = ( $this->value == $this->right_key ) ? 'checked' : '';
        $this->render_content();
    }

    public function array_key_first( array $array ) {

        foreach ( $array as $key => $value ) {return $key;}

    }

    public function render_content() {
        ?>
        <li <?php echo dm_render_markup( $this->default_attributes ); ?>>
            <div class="dm-option-column left">
                <label  class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>
            <div class="dm-option-column right dm_switcher_main_block" >
                <div class='dm_switcher_item' date-right="<?php echo esc_attr( $this->right_key); ?>">
                    <input <?php $this->link(); ?> type='text' class='dm-ctrl' style="display: none;" value='<?php echo esc_attr( $this->left_key ); ?>' name='<?php echo esc_attr( $this->name ); ?>' />
                    <label>
                        <input <?php $this->link(); ?> type='checkbox' class='dm-ctrl dm-control-input dm-control-switcher' value='<?php echo esc_attr( $this->right_key ); ?>' name='<?php echo esc_attr( $this->name ); ?>' <?php echo esc_attr( $this->is_checked ); ?>/>
                        <div data-left="<?php echo esc_attr( $this->left_key ); ?>" data-right="<?php echo esc_attr( $this->right_key ); ?>" class='dm_switcher_label dm-option-label'></div>
                    </label>
                </div>
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>
    <?php
    }
}
