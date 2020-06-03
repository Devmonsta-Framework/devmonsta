<?php
namespace Devmonsta\Options\Customizer\Controls\Switcher;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class Switcher extends \WP_Customize_Control {

    public $label, $name, $desc, $default_value, $value;

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
    }

    /**
     * @internal
     */
    public function enqueue() {
        // js
        wp_enqueue_script( 'dm-switcher', plugins_url( 'switcher/assets/js/dm-switcher.js', dirname( __FILE__ ) ), ['jquery'], time(), true );
        //css
        wp_enqueue_style( 'dm-switcher', plugins_url( 'switcher/assets/css/dm-switcher.css', dirname( __FILE__ ) ) );
    
        $switcher_data['settings_id'] = $this->name;
        $switcher_data['left_key'] = $this->left_choice[$this->left_key];
        $switcher_data['right_key'] = $this->right_choice[$this->right_key];
        wp_localize_script( 'dm-switcher', 'switcher_data', $switcher_data );
    }

    /**
     * @internal
     */
    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? $this->value() : $this->default_value;
        
        var_dump($this->value);
        $this->render_content();
    }

    public function array_key_first( array $array ) {

        foreach ( $array as $key => $value ) {return $key;}

    }

    public function render_content() {
        ?>
        <li class="dm-option">
            <div class="dm-option-column left">
                <label  class="dm-option-label"><?php echo esc_html( $this->label ); ?> </label>
            </div>
            <div class="dm-option-column right dm_switcher_main_block" >
                <div class='dm_switcher_item' date-right="<?php echo esc_attr( $this->right_choice[$this->right_key] ); ?>">
                    <input <?php $this->link();?> type='checkbox' class='dm-ctrl dm-control-input dm_switcher_right' 
                            <?php echo ( $this->value == $this->right_choice[$this->right_key] ) ? 'checked' : ''; ?> />
                    <label data-left="<?php echo esc_attr( $this->left_choice[$this->left_key] ); ?>" data-right="<?php echo esc_attr( $this->right_choice[$this->right_key] ); ?>" class='dm_switcher_label dm-option-label'></label>
                </div>
                <input <?php $this->link();?> class='dm-ctrl dm_switcher_left' type='checkbox' <?php echo ( $this->value == $this->left_choice[$this->left_key] ) ? 'checked' : ''; ?> />
                <input <?php $this->link(); ?> type="hidden" name="<?php $this->name; ?>" class="dm-switcher-value" />
                <p class="dm-option-desc"><?php echo esc_html( $this->desc ); ?> </p>
            </div>
        </li>
    <?php
    }
}
