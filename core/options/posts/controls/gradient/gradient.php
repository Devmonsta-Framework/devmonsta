<?php

namespace Devmonsta\Options\Posts\Controls\Gradient;

use Devmonsta\Options\Posts\Structure;

class Gradient extends Structure {

    protected $value;

    /**
     * @internal
     */
    public function init() {

    }

    /**
     * @internal
     */
    public function enqueue() {
        add_action( 'admin_enqueue_scripts', [$this, 'load_scripts'] );
    }

    /**
     * @internal
     */
    public function load_scripts( $hook ) {
        
		wp_enqueue_style(
			'dm-option-gradient',
            plugins_url( 'gradient/assets/css/styles.css'),
            array(),
			'1.0.0'
        );

        wp_enqueue_style(
			'dm-option-color-picker',
			plugins_url('color-picker/assets/css/styles.css'),
			array(),
			'1.0.0'
		);

		wp_enqueue_script(
			'dm-option-color-picker',
			plugins_url('color-picker/assets/js/scripts.js'),
			array('jquery', 'dm-events', 'wp-color-picker'),
			'1.0.0',
			true
		);

		wp_localize_script(
			'dm-option-color-picker',
			'_dm_option_type_'. str_replace('-', '_', 'color-picker') . '_localized',
			array(
				'dm10n' => array(
					'reset_to_default' => __('Reset', 'devmonsta'),
					'reset_to_initial' => __('Reset', 'devmonsta'),
				),
			)
		);

		wp_register_script(
			'dm-events',
            plugins_url( 'gradient/assets/js/dm-events.js'),
            array(),
            '1.0.0',
			true
        );
    }

    /**
     * @internal
     */
    public function render() {
        $content = $this->content;
        global $post;

        $this->value = !is_null( get_post_meta( $post->ID, $this->prefix . $content['name'], true ) ) ?
                        get_post_meta( $post->ID, $this->prefix . $content['name'], true )
                        : $content['value'];

        $this->output();
    }

    /**
     * @internal
     */
    public function output() {
        $lable = isset( $this->content['label'] ) ? $this->content['label'] : '';
        $name  = isset( $this->content['name'] ) ? $this->content['name'] : '';
        $desc  = isset( $this->content['desc'] ) ? $this->content['desc'] : '';
        $attrs = isset( $this->content['attr'] ) ? $this->content['attr'] : '';
        ?>
        <div>
            <lable><?php echo esc_html( $lable ); ?> </lable>
            <div><small><?php echo esc_html( $desc ); ?> </small></div>
            <input type="text" name="<?php echo esc_html( $this->prefix . $name ); ?>" value="<?php echo esc_html( $this->value ); ?>" >
        </div>
    <?php
}

}
