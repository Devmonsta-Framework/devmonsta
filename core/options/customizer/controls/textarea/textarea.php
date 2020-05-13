<?php
namespace Devmonsta\Options\Customizer\Controls\Textarea;

if ( !class_exists( 'WP_Customize_Control' ) ) {
    return NULL;
}

class Textarea extends \WP_Customize_Control {

    /**
     * @access public
     * @var    string
     */
    public $type = 'textarea';

    public $statuses;

    public function __construct( $manager, $id, $args = [] ) {

        $this->statuses = ['' => __( 'Default' )];
        parent::__construct( $manager, $id, $args );
    }

    public function enqueue() {
    }

    public function content_template() {
        ?>
		<div>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<textarea class="large-text" cols="20" rows="5" <?php $this->link();?>>
				<?php echo esc_textarea( $this->value() ); ?>
			</textarea>
		</label>
		</div>
		<?php
}

}
