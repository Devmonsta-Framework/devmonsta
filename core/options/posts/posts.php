<?php

/**
 * =================================
 *       Handel page , post and
 *      custom post type metabox
 * =================================
 */

namespace Devmonsta\Options\Posts;

use Devmonsta\Libs\Posts as LibsPosts;
use Devmonsta\Traits\Singleton;

class Posts
{
	use Singleton;

	protected $data;

	public $controls_list = [
		'rgba-color-picker',
		'checkbox-multiple',
		'datetime-picker',
		'datetime-range',
		'color-picker',
		'image-picker',
		'range-slider',
		'date-picker',
		'multiselect',
		'typography',
		'dimensions',
		'wp-editor',
		'textarea',
		'switcher',
		'gradient',
		'checkbox',
		'hidden',
		'oembed',
		'upload',
		'select',
		'slider',
		'radio',
		'html',
		'icon',
		'text',
		'url',
	];

	/**
	 * =============================
	 * Bootstrap post functionality
	 *
	 * @return  void
	 * =============================
	 */

	public function init()
	{

		if ( !$this->check_requirements() ) {
			return;
		}

		add_action('admin_init', [$this, 'load_scripts']);
		$this->load_enqueue();

		/** Check if post files exists in active theme directory */

		if (!empty( $this->get_post_files()) ) {

			/** Include the file and stract the data  */
			$files = [];

			foreach ( $this->get_post_files() as $file ) {

				require_once $file;
				$files[] = $file;
				/** Get the class name which is extended to @Devmonsta\Libs\Posts */
			}

			$post_file_class = [];

			foreach ( get_declared_classes() as $class ) {

				if (is_subclass_of($class, 'Devmonsta\Libs\Posts')) {
					$post_file_class[] = $class;
				}
			}

			/** Get all the properties defined in post file */

			$post_lib = new LibsPosts;

			foreach ( $post_file_class as $child_class ) {
				$post_file = new $child_class;

				if (method_exists($post_file, 'register_controls')) {

					$post_file->register_controls();

				}
			}

			/** Get all the metaboxed that has been defined */

			$all_meta_box = $post_lib->all_boxes();

			/**
			 *  Get all controls defined in theme
			 */

			$all_controls = $post_lib->all_controls();

			/**
			 *  Get Post type anem from the file name
			 */
			foreach ( $files as $file_name ) {
				$post_type = basename($file_name, ".php");

				/** Create metabox */

				foreach ( $all_meta_box as $args ) {
					if ( $post_type == $args['post_type'] ) {
						$this->data = $args;

						$this->add_meta_box($post_type, $args, $all_controls);
					}
				}
			}
		}
		add_action( 'save_post', [$this, 'save'] );

	}

	/**
	 * =========================================
	 * Check requirements for bootstraping post.
	 *
	 * @return  boolean
	 * =========================================
	 */

	public function check_requirements()
	{
		//register script for ajax calls
		$this->register_ajax_callbacks();

		global $pagenow;
		if ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) {return true;}
		return false;
	}

	/**
	 * =========================================================
	 *       Load scripts and style files form the controls
	 *
	 * @return  void
	 * =========================================================
	 */
	public function load_enqueue()
	{
		foreach ($this->controls_list as $control)
		{
			$class_name = explode('-', $control);
			$class_name = array_map('ucfirst', $class_name);
			$class_name = implode('', $class_name);
			$control_class = 'Devmonsta\Options\Posts\Controls\\' . $class_name . '\\' . $class_name;

			if (class_exists($control_class))
			{
				$meta_owner = "post";
				$control = new $control_class([
					'id' => '',
					'value' => '',
				]);
				$control->enqueue($meta_owner);
			}

		}

	}

	public function get_post_files()
	{
		$files = [];

		foreach (glob(get_template_directory() . '/devmonsta/options/posts/*.php') as $post_files)
		{
			array_push($files, $post_files);
		}

		return $files;
	}

	/** Add Metabox to the post */

	public function add_meta_box($post_type, $args, $all_controls)
	{

		add_action('add_meta_boxes', function () use ($post_type, $args, $all_controls)
		{
			add_meta_box(
				$args['id'], // Unique ID / metabox ID
				$args['title'], // Box title
				[$this, 'render'], // Content callback, must be of type callable
				$post_type, // Post type
				'normal',
				'high',
				[$args, $all_controls]
			);
		});

	}

	public function render($post_id, $arr)
	{
		$args = $arr['args'][0];
		$all_controls = $arr['args'][1];

		if (!empty($all_controls))
			View::instance()->build($args['id'], $all_controls);

	}

	/**
	 * ========================================
	 *      Find Devmonsta metabox actions
	 *       And save them into database
	 * ========================================
	 */
	public function save($post_id)
	{
		$prefix = 'devmonsta_';
		$controls_data = Controls::get_controls();
		update_option('devmonsta_all_potmeta_controls',$controls_data);

		foreach ($_POST as $key => $value) {

			if (strpos($key, $prefix) !== false) {
				update_post_meta(
					$post_id,
					$key,
					devm_sanitize_data($key,$value)
				);
			}
		}
	}

	/**
	 * ===========================================
	 *      Load Styles & Scripts for controls
	 * ===========================================
	 */
	public function load_scripts()
	{
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
		$colorpicker_l10n = array(
			'clear'            => __( 'Clear' ),
			'clearAriaLabel'   => __( 'Clear color' ),
			'defaultString'    => __( 'Default' ),
			'defaultAriaLabel' => __( 'Select default color' ),
			'pick'             => __( 'Select Color' ),
			'defaultLabel'     => __( 'Color value' ),
		);

		wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
		wp_enqueue_style('dm-main-style', DEVMONSTA_PATH . 'core/options/assets/css/main.css');
		wp_enqueue_script('vue-js', DEVMONSTA_PATH . 'core/options/posts/assets/js/vue.min.js', [], null, false);
		wp_enqueue_script('dm-vendor-js', DEVMONSTA_PATH . 'core/options/assets/js/dm-vendor-scripts.bundle.js', ['jquery'], null, true);
		wp_enqueue_script('dm-init-js', DEVMONSTA_PATH . 'core/options/assets/js/dm-init-scripts.bundle.js', ['jquery'], null, true);
		wp_enqueue_script('dm-color-picker', DEVMONSTA_PATH . 'core/options/posts/assets/js/script.js', [], null, true);
		wp_enqueue_script("jquery-ui-draggable");
		wp_localize_script('dm-repeater', 'ajax_object', [
			'ajax_url' => admin_url('admin-ajax.php')
		]);

	}

	/**
	 * Register all ajax callback functions to be called later
	 *
	 * @return void
	 */
	public function register_ajax_callbacks()
	{
		add_action('wp_ajax_get_oembed_response', ["Devmonsta\Options\Posts\Controls\Oembed\Oembed", 'action_get_oembed_response']);
	}

}