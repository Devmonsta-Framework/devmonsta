<?php
namespace Devmonsta\Options\Customizer\Controls\Gallery;

use Devmonsta\Options\Customizer\Structure;

class Gallery extends Structure {

    public $label, $description, $name, $default_value, $value;

    /**
     * @access public
     * @var    string
     */
    public $type = 'gallery';

    public $statuses;

    public function __construct( $manager, $id, $args = [] ) {
        $this->prepare_values( $id, $args );
        $this->statuses = ['' =>esc_html__( 'Default' )];
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
        $this->description   = isset( $args[0]['description'] ) ? $args[0]['description'] : "";
        $this->name          = isset( $args[0]['id'] ) ? $args[0]['id'] : "";
        $this->default_value = isset( $args[0]['value'] ) ? $args[0]['value'] : "";
    }

    /*
     ** Enqueue control related scripts/styles
     */
    public function enqueue() {
        wp_enqueue_media();
    }


    public function render() {
        $this->value = ( !is_null( $this->value() ) && !empty( $this->value() ) ) ? $this->value() : $this->default_value;
        $this->render_content();
    }

    public function render_content() {
        $img_ids = explode(',', $this->value);
        ?>
        <li id="customize-control-devm_gallery" class="customize-control customize-control-gallery">
            <?php if ( $this->label ): ?>
            <span class="customize-control-title">
                <?php echo esc_html( $this->label ); ?>
            </span>
            <?php endif; ?>

            <div class="customize-control-notifications-container"></div>

            <?php if ( $this->description ): ?>
            <span class="description customize-control-description">
                <?php echo esc_html( $this->description ); ?>
            </span>
            <?php endif; ?>

            <div class="attachment-media-view">
                <div class="devm-option-upload--list is--multiple" data-multiple="1">
                    <?php
                    if ( !empty($this->value) ) {
                        foreach ($img_ids as $img_id) {
                            $img_url = wp_get_attachment_image_src( $img_id, 'large' );
                            ?>
                            <div class="devm-option-upload--item">
                                <img src="<?php echo esc_attr( $img_url[0] ); ?>" class="devm-option-upload--child" />

                                <button type="button" class="devm-option-upload--remove dashicons dashicons-dismiss" data-id="<?php echo esc_attr( $img_id ); ?>"></button>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="devm-option-upload--item has--btn">
                            <button type="button" class="devm-option-upload--child button">
                                <?php echo esc_html__( 'Upload Images', 'devmonsta' ); ?>
                            </button>
                        </div>
                        <?php
                    }
                    ?>
                </div>

                <input type="hidden" name="<?php echo esc_attr( $this->name ); ?>" value="<?php echo esc_attr( $this->value ); ?>" <?php echo $this->get_link(); ?>>
            </div>
        </li>
        <?php
    }
}
