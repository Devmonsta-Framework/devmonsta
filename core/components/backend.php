<?php if (!defined('DMS')) {
	die('Forbidden');
}

/**
 * Backend functionality
 */
final class _DM_Component_Backend
{

	/** @var DM_Settings_Form */
	private $settings_form;

	private $available_render_designs = array(
		'default', 'taxonomy', 'customizer', 'empty'
	);

	private $default_render_design = 'default';

	/**
	 * The singleton instance of Parsedown class that is used across
	 * whole framework.
	 *
	 * @since 2.6.9
	 */
	private $markdown_parser = null;

	/**
	 * Contains all option types
	 * @var DM_Option_Type[]
	 */
	private $option_types = array();

	/**
	 * @var DM_Option_Type_Undefined
	 */
	private $undefined_option_type;

	/**
	 * Store container types for registration, until they will be required
	 * @var array|false
	 *      array Can have some pending container types in it
	 *      false Container types already requested and was registered, so do not use pending anymore
	 */
	private $container_types_pending_registration = array();

	/**
	 * Contains all container types
	 * @var DM_Container_Type[]
	 */
	private $container_types = array();

	/**
	 * @var DM_Container_Type_Undefined
	 */
	private $undefined_container_type;

	private $static_registered = false;

	/**
	 * @var DM_Access_Key
	 */
	private $access_key;

	/**
	 * @internal
	 */
	public function _get_settings_page_slug()
	{
		return 'dm-settings';
	}

	/**
	 * @return string
	 * @since 2.6.3
	 */
	public function get_options_name_attr_prefix()
	{
		return 'dm_options';
	}

	/**
	 * @return string
	 * @since 2.6.3
	 */
	public function get_options_id_attr_prefix()
	{
		return 'dm-option-';
	}

	private function get_current_edit_taxonomy()
	{
		static $cache_current_taxonomy_data = null;

		if ($cache_current_taxonomy_data !== null) {
			return $cache_current_taxonomy_data;
		}

		$result = array(
			'taxonomy' => null,
			'term_id'  => 0,
		);

		do {
			if (!is_admin()) {
				break;
			}

			// code from /wp-admin/admin.php line 110
			{
				if (isset($_REQUEST['taxonomy']) && taxonomy_exists($_REQUEST['taxonomy'])) {
					$taxnow = $_REQUEST['taxonomy'];
				} else {
					$taxnow = '';
				}
			}

			if (empty($taxnow)) {
				break;
			}

			$result['taxonomy'] = $taxnow;

			if (empty($_REQUEST['tag_ID'])) {
				return $result;
			}

			// code from /wp-admin/edit-tags.php
			{
				$tag_ID = (int) $_REQUEST['tag_ID'];
			}

			$result['term_id'] = $tag_ID;
		} while (false);

		$cache_current_taxonomy_data = $result;

		return $cache_current_taxonomy_data;
	}

	public function __construct()
	{
	}

	/**
	 * @internal
	 */
	public function _init()
	{
		if (is_admin()) {
			$this->settings_form = new DM_Settings_Form_Theme('theme-settings');
		}

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * @internal
	 */
	public function _after_components_init()
	{
	}

	private function get_access_key()
	{
		if (!$this->access_key) {
			$this->access_key = new DMS_Access_Key('dms_backend');
		}

		return $this->access_key;
	}

	private function add_actions()
	{
		if (is_admin()) {
			add_action('add_meta_boxes', array($this, '_action_create_post_meta_boxes'), 10, 2);
			add_action('init', array($this, '_action_init'), 20);
			add_action(
				'admin_enqueue_scripts',
				array($this, '_action_admin_register_scripts'),

				8
			);
			add_action(
				'admin_enqueue_scripts',
				array($this, '_action_admin_enqueue_scripts'),

				11
			);
			add_action('admin_menu', array($this, '_action_admin_menu'));

			// render and submit options from javascript
			{
				add_action('wp_ajax_dm_backend_options_render', array($this, '_action_ajax_options_render'));
				add_action('wp_ajax_dm_backend_options_get_values', array($this, '_action_ajax_options_get_values'));
				add_action('wp_ajax_dm_backend_options_get_values_json', array($this, '_action_ajax_options_get_values_json'));
			}
		}

		add_action('save_post', array($this, '_action_save_post'), 7, 3);
		add_action('wp_restore_post_revision', array($this, '_action_restore_post_revision'), 10, 2);
		add_action('_wp_put_post_revision', array($this, '_action__wp_put_post_revision'));

		add_action('customize_register', array($this, '_action_customize_register'), 7);
	}

	public function _action_admin_menu()
	{

		$parent_slug = 'index.php';
		$menu_title  = esc_html__('New', 'dm');

		if (isset($GLOBALS['admin_page_hooks'])) {
			$parent_slug = 'dm-extensions';
			$menu_title  = esc_html__('New', 'dm');
		}

		add_submenu_page(
			$parent_slug,
			esc_html__('New', 'dm'),
			$menu_title,
			'manage_options',
			'dm-new',
			array($this, 'render_about_page')
		);
	}

	public function render_about_page()
	{

		$file = WP_PLUGIN_DIR . '/devmonsta/framework/views/about.php';

		if (file_exists($file)) {
			include $file;
		}
	}

	private function add_filters()
	{
	}

	/**
	 * @param string|DM_Option_Type $option_type_class
	 * @param string|null $type
	 *
	 * @internal
	 */
	private function register_option_type($option_type_class, $type = null)
	{
		if ($type == null) {
			try {
				$type = $this->get_instance($option_type_class)->get_type();
			} catch (DMS_Option_Type_Exception_Invalid_Class $exception) {
				if (!is_subclass_of($option_type_class, 'DMS_Option_Type')) {
					trigger_error('Invalid option type class ' . get_class($option_type_class), E_USER_WARNING);

					return;
				}
			}
		}

		if (isset($this->option_types[$type])) {
			trigger_error('Option type "' . $type . '" already registered', E_USER_WARNING);

			return;
		}

		$this->option_types[$type] = $option_type_class;
	}

	/**
	 * @param string|DMS_Container_Type $container_type_class
	 * @param string|null $type
	 *
	 * @internal
	 */
	private function register_container_type($container_type_class, $type = null)
	{
		if ($type == null) {
			try {
				$type = $this->get_instance($container_type_class)->get_type();
			} catch (DMS_Option_Type_Exception_Invalid_Class $exception) {
				if (!is_subclass_of($container_type_class, 'DMS_Container_Type')) {
					trigger_error('Invalid container type class ' . get_class($container_type_class), E_USER_WARNING);

					return;
				}
			}
		}

		if (isset($this->container_types[$type])) {
			trigger_error('Container type "' . $type . '" already registered', E_USER_WARNING);

			return;
		}

		$this->container_types[$type] = $container_type_class;
	}

	private function register_static()
	{
		if (
			!doing_action('admin_enqueue_scripts')
			&&
			!did_action('admin_enqueue_scripts')
		) {

			return;
		}

		if ($this->static_registered) {
			return;
		}

		if (!is_admin()) {
			$this->static_registered = true;

			return;
		}

		wp_register_script(
			'dm-events',
			dms_get_framework_directory_uri('/static/js/dm-events.js'),
			array(),
			dm()->manifest->get_version(),
			true
		);

		wp_register_script(
			'dm-ie-fixes',
			dms_get_framework_directory_uri('/static/js/ie-fixes.js'),
			array(),
			dm()->manifest->get_version(),
			true
		); {
			wp_register_style(
				'qtip',
				dms_get_framework_directory_uri('/static/libs/qtip/css/jquery.qtip.min.css'),
				array(),
				dm()->manifest->get_version()
			);
			wp_register_script(
				'qtip',
				dms_get_framework_directory_uri('/static/libs/qtip/jquery.qtip.min.js'),
				array('jquery'),
				dm()->manifest->get_version()
			);
		} {
			wp_register_style(
				'dms',
				dms_get_framework_directory_uri('/static/css/dms.css'),
				array('qtip'),
				dm()->manifest->get_version()
			);

			wp_register_script(
				'dms-reactive-options-registry',
				dms_get_framework_directory_uri(
					'/static/js/dms-reactive-options-registry.js'
				),
				array('dms', 'dm-events'),
				false
			);

			wp_register_script(
				'dms-reactive-options-simple-options',
				dms_get_framework_directory_uri(
					'/static/js/dms-reactive-options-simple-options.js'
				),
				array('dms', 'dm-events', 'dms-reactive-options-undefined-option'),
				false
			);

			wp_register_script(
				'dms-reactive-options-undefined-option',
				dms_get_framework_directory_uri(
					'/static/js/dms-reactive-options-undefined-option.js'
				),
				array(
					'dms', 'dm-events', 'dms-reactive-options-registry'
				),
				false
			);

			wp_register_script(
				'dms-reactive-options',
				dms_get_framework_directory_uri('/static/js/dms-reactive-options.js'),
				array(
					'dms', 'dm-events', 'dms-reactive-options-undefined-option',
					'dms-reactive-options-simple-options'
				),
				false
			);

			wp_register_script(
				'dms',
				dms_get_framework_directory_uri('/static/js/dms.js'),
				array('jquery', 'dm-events', 'backbone', 'qtip'),
				dm()->manifest->get_version(),
				false
			);

			wp_localize_script('dms', '_dms_localized', array(
				'DMS_URI'     => dms_get_framework_directory_uri(),
				'SITE_URI'   => site_url(),
				'LOADER_URI' => apply_filters('dms_loader_image', dms_get_framework_directory_uri() . '/static/img/logo.svg'),
				'l10n'       => array_merge(
					$l10n = array(
						'modal_save_btn' => __('Save', 'dms'),
						'done'     => __('Done', 'dms'),
						'ah_sorry' => __('Ah, Sorry', 'dms'),
						'reset'    => __('Reset', 'dms'),
						'apply'    => __('Apply', 'dms'),
						'cancel'   => __('Cancel', 'dms'),
						'ok'       => __('Ok', 'dms')
					),

					apply_filters('dms_js_l10n', $l10n)
				),
				'options_modal' => array(
					/** @since 2.6.13 */
					'default_reset_bnt_disabled' => apply_filters('dms:option-modal:default:reset-btn-disabled', false)
				),
			));
		} {
			wp_register_style(
				'dms-backend-options',
				dms_get_framework_directory_uri('/static/css/backend-options.css'),
				array('dms'),
				dm()->manifest->get_version()
			);

			wp_register_script(
				'dms-backend-options',
				dms_get_framework_directory_uri('/static/js/backend-options.js'),
				array('dms', 'dm-events', 'dms-reactive-options', 'postbox', 'jquery-ui-tabs'),
				dm()->manifest->get_version(),
				true
			);

			wp_localize_script('dms', '_dms_backend_options_localized', array(
				'lazy_tabs' => dm()->theme->get_config('lazy_tabs')
			));
		} {
			wp_register_style(
				'dms-selectize',
				dms_get_framework_directory_uri('/static/libs/selectize/selectize.css'),
				array(),
				dm()->manifest->get_version()
			);
			wp_register_script(
				'dms-selectize',
				dms_get_framework_directory_uri('/static/libs/selectize/selectize.min.js'),
				array('jquery', 'dm-ie-fixes'),
				dm()->manifest->get_version(),
				true
			);
		} {
			wp_register_script(
				'dms-mousewheel',
				dms_get_framework_directory_uri('/static/libs/mousewheel/jquery.mousewheel.min.js'),
				array('jquery'),
				dm()->manifest->get_version(),
				true
			);
		} {
			wp_register_style(
				'dms-jscrollpane',
				dms_get_framework_directory_uri('/static/libs/jscrollpane/jquery.jscrollpane.css'),
				array(),
				dm()->manifest->get_version()
			);
			wp_register_script(
				'dms-jscrollpane',
				dms_get_framework_directory_uri('/static/libs/jscrollpane/jquery.jscrollpane.min.js'),
				array('jquery', 'dms-mousewheel'),
				dm()->manifest->get_version(),
				true
			);
		}

		wp_register_style(
			'font-awesome',
			dms_get_framework_directory_uri('/static/libs/font-awesome/css/font-awesome.min.css'),
			array(),
			dm()->manifest->get_version()
		);

		wp_register_style('dms-font-awesome', dms_get_framework_directory_uri('/static/libs/font-awesome/css/font-awesome.min.css'));

		wp_register_script(
			'backbone-relational',
			dms_get_framework_directory_uri('/static/libs/backbone-relational/backbone-relational.js'),
			array('backbone'),
			dm()->manifest->get_version(),
			true
		);

		wp_register_script(
			'dms-uri',
			dms_get_framework_directory_uri('/static/libs/uri/URI.js'),
			array(),
			dm()->manifest->get_version(),
			true
		);

		wp_register_script(
			'dms-moment',

			dms_get_framework_directory_uri('/static/libs/moment/moment-with-locales.min.js'),
			array(),
			dm()->manifest->get_version(),
			true
		);

		wp_register_script(
			'dms-form-helpers',
			dms_get_framework_directory_uri('/static/js/dms-form-helpers.js'),
			array('jquery'),
			dm()->manifest->get_version(),
			true
		);

		wp_register_style(
			'dms-unycon',
			dms_get_framework_directory_uri('/static/libs/unycon/unycon.css'),
			array(),
			dm()->manifest->get_version()
		);

		$this->static_registered = true;
	}

	/**
	 * @param $class
	 *
	 * @return DMS_Option_Type
	 * @throws DMS_Option_Type_Exception_Invalid_Class
	 */
	protected function get_instance($class)
	{
		if (
			!class_exists($class)
			|| (!is_subclass_of($class, 'DMS_Option_Type')
				&&
				!is_subclass_of($class, 'DMS_Container_Type'))
		) {
			throw new DMS_Option_Type_Exception_Invalid_Class($class);
		}

		return new $class;
	}


	/**
	 * @param string $post_type
	 * @param WP_Post $post
	 */
	public function _action_create_post_meta_boxes($post_type, $post)
	{
		if ('comment' === $post_type || (isset($_GET['vc_action']) && $_GET['vc_action'] === 'vc_inline')) {

			return;
		}

		$options = dm()->theme->get_post_options($post_type);

		if (empty($options)) {
			return;
		}

		$collected = array();

		dms_collect_options($collected, $options, array(
			'limit_option_types'    => false,
			'limit_container_types' => false,
			'limit_level'           => 1,
		));

		if (empty($collected)) {
			return;
		}

		$values = dms_get_db_post_option($post->ID);

		foreach ($collected as $id => &$option) {

			if (isset($option['options']) && ($option['type'] === 'box' || $option['type'] === 'group')) {
				$context  = isset($option['context']) ? $option['context'] : 'normal';
				$priority = isset($option['priority']) ? $option['priority'] : 'default';

				add_meta_box(
					"dms-options-box-{$id}",
					empty($option['title']) ? ' ' : $option['title'],
					array($this, 'render_meta_box'),
					$post_type,
					$context,
					$priority,
					array('dms_box_html' => $this->render_options($option['options'], $values))
				);
			} else { // this is not a box, wrap it in auto-generated box
				add_meta_box(
					'dms-options-box:auto-generated:' . time() . ':' . dms_unique_increment(),
					' ',
					array($this, 'render_meta_box'),
					$post_type,
					'normal',
					'default',
					$this->render_options(array($id => $option), $values)
				);
			}
		}
	}

	public function render_meta_box($post, $args)
	{
		if (empty($args['args'])) {
			return;
		}

		if (isset($args['args']['dms_box_html'])) {
			echo DMS_Helpers::render($args['args']['dms_box_html']);
		} elseif (!is_array($args['args'])) {
			echo DMS_Helpers::render($args['args']);
		}
	}

	/**
	 * @param object $term
	 */
	public function _action_create_taxonomy_options($term)
	{
		$options = dm()->theme->get_taxonomy_options($term->taxonomy);

		if (empty($options)) {
			return;
		}

		$collected = array();

		dms_collect_options($collected, $options, array(
			'limit_option_types' => false,
			'limit_container_types' => false,
			'limit_level' => 1,
		));

		if (empty($collected)) {
			return;
		}

		$values = dms_get_db_term_option($term->term_id, $term->taxonomy);

		// fixes word_press style: .form-field input { width: 95% }
		echo '<style type="text/css">.dms-option-type-radio input, .dms-option-type-checkbox input { width: auto; }</style>';

		do_action('dms_backend_options_render:taxonomy:before');
		echo DMS_Helpers::render($this->render_options($collected, $values, array(), 'taxonomy'));
		do_action('dms_backend_options_render:taxonomy:after');
	}

	/**
	 * @param string $taxonomy
	 */
	public function _action_create_add_taxonomy_options($taxonomy)
	{
		$options = dm()->theme->get_taxonomy_options($taxonomy);

		if (empty($options)) {
			return;
		}

		$collected = array();

		dms_collect_options($collected, $options, array(
			'limit_option_types'    => false,
			'limit_container_types' => false,
			'limit_level'           => 1,
		));

		if (empty($collected)) {
			return;
		}

		// fixes word_press style: .form-field input { width: 95% }
		echo '<style type="text/css">.dms-option-type-radio input, .dms-option-type-checkbox input { width: auto; }</style>';

		do_action('dms_backend_options_render:taxonomy:before');

		echo '<div class="dms-force-xs">';
		echo $this->render_options($collected, array(), array(), 'taxonomy');
		echo '</div>';

		do_action('dms_backend_options_render:taxonomy:after');

		echo '<script type="text/javascript">'
			. 'jQuery(function($){'
			. '    $("#submit").on("click", function(){'
			. '        $("html, body").animate({ scrollTop: $("#col-left").offset().top });'
			. '    });'
			. '});'
			. '</script>';
	}

	public function _action_init()
	{
		$current_edit_taxonomy = $this->get_current_edit_taxonomy();

		if ($current_edit_taxonomy['taxonomy']) {
			add_action(
				$current_edit_taxonomy['taxonomy'] . '_edit_form',
				array($this, '_action_create_taxonomy_options')
			);

			if (dm()->theme->get_config('taxonomy_create_has_devmonsta_options', true)) {
				add_action(
					$current_edit_taxonomy['taxonomy'] . '_add_form_fields',
					array($this, '_action_create_add_taxonomy_options')
				);
			}
		}

		if (!empty($_POST)) {
			// is form submit
			add_action('edited_term', array($this, '_action_term_edit'), 10, 3);

			if ($current_edit_taxonomy['taxonomy']) {
				add_action(
					'create_' . $current_edit_taxonomy['taxonomy'],
					array($this, '_action_save_taxonomy_fields')
				);
			}
		}
	}

	/**
	 * Save meta from $_POST to dms options (post meta)
	 * @param int $post_id
	 * @param WP_Post $post
	 * @param bool $update
	 */
	public function _action_save_post($post_id, $post, $update)
	{

		if (
			isset($_POST['post_ID'])
			&&
			intval($_POST['post_ID']) === intval($post_id)
			&&
			!empty($_POST[$this->get_options_name_attr_prefix()]) // this happens on Quick Edit
		) {
			/**
			 * This happens on regular post form submit
			 * All data from $_POST belongs this $post
			 * so we save them in its post meta
			 */

			static $post_options_save_happened = false;
			if ($post_options_save_happened) {
				/**
				 * Prevent multiple options save for same post
				 * It can happen from a recursion or wp_update_post() for same post id
				 */
				return;
			} else {
				$post_options_save_happened = true;
			}

			$old_values = (array) dms_get_db_post_option($post_id);

			dms_set_db_post_option(
				$post_id,
				null,
				dms_get_options_values_from_input(
					dm()->theme->get_post_options($post->post_type)
				)
			);

			/**
			 * @deprecated
			 * Use the 'dms_post_options_update' action
			 */
			do_action('dms_save_post_options', $post_id, $post, $old_values);
		} elseif ($original_post_id = wp_is_post_autosave($post_id)) {

			do {
				$parent = get_post($post->post_parent);

				if (!$parent instanceof WP_Post) {
					break;
				}

				if (isset($_POST['post_ID']) && intval($_POST['post_ID']) === intval($parent->ID)) {
				} else {
					break;
				}

				if (empty($_POST[$this->get_options_name_attr_prefix()])) {
					// this happens on Quick Edit
					break;
				}

				dms_set_db_post_option(
					$post->ID,
					null,
					dms_get_options_values_from_input(
						dm()->theme->get_post_options($parent->post_type)
					)
				);
			} while (false);
		} elseif ($original_post_id = wp_is_post_revision($post_id)) {

			/**
			 * Do nothing, the
			 * - '_wp_put_post_revision'
			 * - 'wp_restore_post_revision'
			 * actions will handle this
			 */
		} else {
			/**
			 * This happens on:
			 * - post add (auto-draft): do nothing
			 * - revision restore: do nothing, that is handled by the 'wp_restore_post_revision' action
			 */
		}
	}

	/**
	 * @param $revision_id
	 */
	public function _action__wp_put_post_revision($revision_id)
	{
		/**
		 * Copy options meta from post to revision
		 */
		dms_set_db_post_option(
			$revision_id,
			null,
			(array) dms_get_db_post_option(
				wp_is_post_revision($revision_id),
				null,
				array()
			)
		);
	}

	/**
	 * @param $post_id
	 * @param $revision_id
	 */
	public function _action_restore_post_revision($post_id, $revision_id)
	{
		/**
		 * Copy options meta from revision to post
		 */
		dms_set_db_post_option(
			$post_id,
			null,
			(array) dms_get_db_post_option($revision_id, null, array())
		);
	}

	public function _sync_post_separate_meta($post_id)
	{
		if (!($post_type = get_post_type($post_id))) {
			return false;
		}

		$meta_prefix = 'dms_option:';
		$only_options = dms_extract_only_options(dm()->theme->get_post_options($post_type));
		$separate_meta_options = array(); {
			$options_values = dms_get_db_post_option($post_id);

			foreach ($only_options as $option_id => $option) {
				if (
					isset($option['save-in-separate-meta'])
					&&
					$option['save-in-separate-meta']
					&&
					array_key_exists($option_id, $options_values)
				) {
					if (defined('WP_DEBUG') && WP_DEBUG) {
						DMS_Flash_Messages::add(
							'save-in-separate-meta:deprecated',
							'<p>The <code>save-in-separate-meta</code> option parameter is <strong>deprecated</strong>.</p>'
								. '<p>Please replace</p>'
								. '<pre>\'save-in-separate-meta\' => true</pre>'
								. '<p>with</p>'
								. '<pre>\'dms-storage\' => array('
								. "\n	'type' => 'post-meta',"
								. "\n	'post-meta' => 'dms_option:{your-option-id}',"
								. "\n)</pre>"
								. '<p>in <code>{theme}' . dms_get_framework_customizations_dir_rel_path('/theme/options/posts/' . $post_type . '.php') . '</code></p>'
								. '<p><a href="' . esc_url('http://manual.devmonsta.io/en/latest/options/storage.html#content') . '" target="_blank">' . esc_html__('Info about dms-storage', 'dms') . '</a></p>',
							'warning'
						);
					}

					$separate_meta_options[$meta_prefix . $option_id] = $options_values[$option_id];
				}
			}

			unset($options_values);
		}

		// Delete meta that starts with $meta_prefix
		{
			/** @var wpdb $wpdb */
			global $wpdb;

			foreach ($wpdb->get_results(
					$wpdb->prepare(
						"SELECT meta_key " .
							"FROM {$wpdb->postmeta} " .
							"WHERE meta_key LIKE %s AND post_id = %d",
						$wpdb->esc_like($meta_prefix) . '%',
						$post_id
					)
				) as $row) {
				if (
					array_key_exists($row->meta_key, $separate_meta_options)
					||
					( // skip options containing 'dms-storage'
						($option_id = substr($row->meta_key, 10))
						&&
						isset($only_options[$option_id]['dms-storage']))
				) {
					/**
					 * This meta exists and will be updated below.
					 * Do not delete for performance reasons, instead of delete->insert will be performed only update
					 */
					continue;
				} else {
					// this option does not exist anymore
					delete_post_meta($post_id, $row->meta_key);
				}
			}
		}

		foreach ($separate_meta_options as $meta_key => $option_value) {
			dms_update_post_meta($post_id, $meta_key, $option_value);
		}

		return true;
	}

	/**
	 * @param int $term_id
	 */
	public function _action_save_taxonomy_fields($term_id)
	{
		if (
			isset($_POST['action'])
			&&
			'add-tag' === $_POST['action']
			&&
			isset($_POST['taxonomy'])
			&&
			($taxonomy = get_taxonomy($_POST['taxonomy']))
			&&
			current_user_can($taxonomy->cap->edit_terms)
		) { /* ok */
		} else {
			return;
		}

		$options = dm()->theme->get_taxonomy_options($taxonomy->name);
		if (empty($options)) {
			return;
		}

		dms_set_db_term_option(
			$term_id,
			$taxonomy->name,
			null,
			dms_get_options_values_from_input($options)
		);

		do_action('dms_save_term_options', $term_id, $taxonomy->name, array());
	}

	public function _action_term_edit($term_id, $tt_id, $taxonomy)
	{
		if (
			isset($_POST['action'])
			&&
			'editedtag' === $_POST['action']
			&&
			isset($_POST['taxonomy'])
			&&
			($taxonomy = get_taxonomy($_POST['taxonomy']))
			&&
			current_user_can($taxonomy->cap->edit_terms)
		) { /* ok */
		} else {
			return;
		}

		if (intval(DMS_Request::POST('tag_ID')) != $term_id) {
			// the $_POST values belongs to another term, do not save them into this one
			return;
		}

		$options = dm()->theme->get_taxonomy_options($taxonomy->name);
		if (empty($options)) {
			return;
		}

		$old_values = (array) dms_get_db_term_option($term_id, $taxonomy->name);

		dms_set_db_term_option(
			$term_id,
			$taxonomy->name,
			null,
			dms_get_options_values_from_input($options)
		);

		do_action('dms_save_term_options', $term_id, $taxonomy->name, $old_values);
	}

	public function _action_admin_register_scripts()
	{
		$this->register_static();
	}

	public function _action_admin_enqueue_scripts()
	{
		/**
		 * Enqueue settings options static in <head>
		 * @see DMS_Settings_Form_Theme::_action_admin_enqueue_scripts()
		 */

		/**
		 * Enqueue post options static in <head>
		 */ {
			if ('post' === get_current_screen()->base && get_the_ID()) {
				dm()->backend->enqueue_options_static(
					dm()->theme->get_post_options(get_post_type())
				);

				do_action('dms_admin_enqueue_scripts:post', get_post());
			}
		}

		/**
		 * Enqueue term options static in <head>
		 */ {
			if (
				in_array(get_current_screen()->base, array('edit-tags', 'term'), true)
				&&
				get_current_screen()->taxonomy
			) {
				dm()->backend->enqueue_options_static(
					dm()->theme->get_taxonomy_options(get_current_screen()->taxonomy)
				);

				do_action('dms_admin_enqueue_scripts:term', get_current_screen()->taxonomy);
			}
		}
	}

	/**
	 * Render options html from input json
	 *
	 * POST vars:
	 * - options: '[{option_id: {...}}, {option_id: {...}}, ...]'                  // Required // String JSON
	 * - values:  {option_id: value, option_id: {...}, ...}                        // Optional // Object
	 * - data:    {id_prefix: 'dms_options-a-b-', name_prefix: 'dms_options[a][b]'}  // Optional // Object
	 */
	public function _action_ajax_options_render()
	{
		// options
		{
			if (!isset($_POST['options'])) {
				wp_send_json_error(array(
					'message' => 'No options'
				));
			}

			$options = json_decode(DMS_Request::POST('options'), true);

			if (!$options) {
				wp_send_json_error(array(
					'message' => 'Wrong options'
				));
			}
		}

		// values
		{
			if (isset($_POST['values'])) {
				$values = DMS_Request::POST('values');

				if (is_string($values)) {
					$values = json_decode($values, true);
				}
			} else {
				$values = array();
			}

			$filtered_values = apply_filters(
				'dms:ajax_options_render:values',
				null,
				$options,
				$values
			);

			$values = $filtered_values ? $filtered_values : array_intersect_key(
				$values,
				dms_extract_only_options($options)
			);
		}

		// data
		{
			if (isset($_POST['data'])) {
				$data = DMS_Request::POST('data');
			} else {
				$data = array();
			}
		}

		wp_send_json_success(array(
			'html' => dm()->backend->render_options($options, $values, $data),
			/** @since 2.6.1 */
			'default_values' => dms_get_options_values_from_input($options, array()),
		));
	}

	/**
	 * Get options values from html generated with 'dms_backend_options_render' ajax action
	 *
	 * POST vars:
	 * - options: '[{option_id: {...}}, {option_id: {...}}, ...]' // Required // String JSON
	 * - dms_options... // Use a jQuery "ajax form submit" to emulate real form submit
	 *
	 * Tip: Inside form html, add: <input type="hidden" name="options" value="[...json...]">
	 */
	public function _action_ajax_options_get_values()
	{
		// options
		{
			if (!isset($_POST['options'])) {
				wp_send_json_error(array(
					'message' => 'No options'
				));
			}

			$options = DMS_Request::POST('options');

			if (is_string($options)) {
				$options = json_decode(DMS_Request::POST('options'), true);
			}

			if (!$options) {
				wp_send_json_error(array(
					'message' => 'Wrong options'
				));
			}
		}

		// name_prefix
		{
			if (isset($_POST['name_prefix'])) {
				$name_prefix = DMS_Request::POST('name_prefix');
			} else {
				$name_prefix = $this->get_options_name_attr_prefix();
			}
		}

		wp_send_json_success(array(
			'values' => dms_get_options_values_from_input(
				$options,
				DMS_Request::POST(dms_html_attr_name_to_array_multi_key($name_prefix), array())
			)
		));
	}

	/**
	 * Get options values from html generated with 'dms_backend_options_render' ajax action
	 *
	 * POST vars:
	 * - options: '[{option_id: {...}}, {option_id: {...}}, ...]' // Required // String JSON
	 * - values: {option_id: {...}}
	 *
	 * Tip: Inside form html, add: <input type="hidden" name="options" value="[...json...]">
	 */
	public function _action_ajax_options_get_values_json()
	{
		// options
		{
			if (!isset($_POST['options'])) {
				wp_send_json_error(array(
					'message' => 'No options'
				));
			}

			$options = DMS_Request::POST('options');

			if (is_string($options)) {
				$options = json_decode(DMS_Request::POST('options'), true);
			}

			if (!$options) {
				wp_send_json_error(array(
					'message' => 'Wrong options'
				));
			}
		}

		// values
		{
			if (!isset($_POST['values'])) {
				wp_send_json_error(array(
					'message' => 'No values'
				));
			}

			$values = DMS_Request::POST('values');

			if (is_string($values)) {
				$values = json_decode(DMS_Request::POST('values'), true);
			}

			if (!is_array($values)) {
				if (!$values) {
					wp_send_json_error(array(
						'message' => 'Wrong values'
					));
				}
			}
		}

		wp_send_json_success(array(
			'values' => dms_get_options_values_from_input(
				$options,
				$values
			)
		));
	}

	public function render_options($options, $values = array(), $options_data = array(), $design = null)
	{
		if (empty($design)) {
			$design = $this->default_render_design;
		}

		if (
			!doing_action('admin_enqueue_scripts')
			&&
			!did_action('admin_enqueue_scripts')
		) {
		} else {

			$this->register_static();

			wp_enqueue_media();
			wp_enqueue_style('dms-backend-options');
			wp_enqueue_script('dms-backend-options');
		}

		$collected = array();

		dms_collect_options($collected, $options, array(
			'limit_option_types' => false,
			'limit_container_types' => false,
			'limit_level' => 1,
			'info_wrapper' => true,
		));

		if (empty($collected)) {
			return false;
		}

		$html = '';

		$option = reset($collected);

		$collected_type = array(
			'group' => $option['group'],
			'type'  => $option['option']['type'],
		);
		$collected_type_options = array(
			$option['id'] => &$option['option']
		);

		while ($collected_type_options) {
			$option = next($collected);

			if ($option) {
				if (
					$option['group'] === $collected_type['group']
					&&
					$option['option']['type'] === $collected_type['type']
				) {
					$collected_type_options[$option['id']] = &$option['option'];
					continue;
				}
			}

			switch ($collected_type['group']) {
				case 'container':
					if ($design === 'taxonomy') {
						$html .= dms_render_view(
							dms_get_framework_directory('/views/backend-container-design-' . $design . '.php'),
							array(
								'type' => $collected_type['type'],
								'html' => $this->container_type($collected_type['type'])->render(
									$collected_type_options,
									$values,
									$options_data
								),
							)
						);
					} else {
						$html .= $this->container_type($collected_type['type'])->render(
							$collected_type_options,
							$values,
							$options_data
						);
					}
					break;
				case 'option':
					foreach ($collected_type_options as $id => &$_option) {
						$data = $options_data; // do not change directly to not affect next loops

						$maybe_future_value = apply_filters(
							'dms:render_options:option_value',
							null,
							$values,
							$_option,
							$id
						);

						if (!$maybe_future_value) {
							$maybe_future_value = isset($values[$id]) ? $values[$id] : null;
						}

						$data['value'] = $maybe_future_value;

						$html .= $this->render_option(
							$id,
							$_option,
							$data,
							$design
						);
					}
					unset($_option);
					break;
				default:
					$html .= '<p><em>' . __('Unknown collected group', 'dms') . ': ' . $collected_type['group'] . '</em></p>';
			}

			unset($collected_type, $collected_type_options);

			if ($option) {
				$collected_type = array(
					'group' => $option['group'],
					'type'  => $option['option']['type'],
				);
				$collected_type_options = array(
					$option['id'] => &$option['option']
				);
			} else {
				$collected_type_options = array();
			}
		}

		return $html;
	}


	public function enqueue_options_static($options)
	{
		static $static_enqueue = true;

		if (
			!doing_action('admin_enqueue_scripts')
			&&
			!did_action('admin_enqueue_scripts')
		) {

			return;
		} else {

			if ($static_enqueue) {
				$this->register_static();

				wp_enqueue_media();
				wp_enqueue_style('dms-backend-options');
				wp_enqueue_script('dms-backend-options');

				$static_enqueue = false;
			}
		}

		$collected = array();

		dms_collect_options($collected, $options, array(
			'limit_option_types' => false,
			'limit_container_types' => false,
			'limit_level' => 0,
			'callback' => array(__CLASS__, '_callback_dms_collect_options_enqueue_static'),
		));

		unset($collected);
	}

	/**
	 * @internal
	 * @param array $data
	 */
	public static function _callback_dms_collect_options_enqueue_static($data)
	{
		if ($data['group'] === 'option') {
			dm()->backend->option_type($data['option']['type'])->enqueue_static($data['id'], $data['option']);
		} elseif ($data['group'] === 'container') {
			dm()->backend->container_type($data['option']['type'])->enqueue_static($data['id'], $data['option']);
		}
	}

	public function render_option($id, $option, $data = array(), $design = null)
	{

		$maybe_forced_design = dm()->backend->option_type($option['type'])->get_forced_render_design();

		if (empty($design)) {
			$design = $this->default_render_design;
		}

		if ($maybe_forced_design) {
			$design = $maybe_forced_design;
		}

		if (
			!doing_action('admin_enqueue_scripts')
			&&
			!did_action('admin_enqueue_scripts')
		) {
		} else {
			$this->register_static();
		}


		if (!in_array($design, $this->available_render_designs)) {
			trigger_error('Invalid render design specified: ' . $design, E_USER_WARNING);
			$design = 'post';
		}

		if (!isset($data['id_prefix'])) {
			$data['id_prefix'] = $this->get_options_id_attr_prefix();
		}

		$data = apply_filters(
			'dms:backend:option-render:data',
			$data
		);

		return dms_render_view(dms_get_framework_directory('/views/backend-option-design-' . $design . '.php'), array(
			'id'     => $id,
			'option' => $option,
			'data'   => $data,
		));
	}


	public function render_box($id, $title, $content, $other = array())
	{
		if (!function_exists('add_meta_box')) {
			trigger_error(
				'Try call this method later (\'admin_init\' action), add_meta_box() function does not exists yet.',
				E_USER_WARNING
			);

			return '';
		}

		$other = array_merge(array(
			'html_before_title' => false,
			'html_after_title'  => false,
			'attr'              => array(),
		), $other); {
			$placeholders = array(
				'id'      => '{{meta_box_id}}',
				'title'   => '{{meta_box_title}}',
				'content' => '{{meta_box_content}}',
			);

			// other placeholders
			{
				$placeholders['html_before_title'] = '{{meta_box_html_before_title}}';
				$placeholders['html_after_title']  = '{{meta_box_html_after_title}}';
				$placeholders['attr']              = '{{meta_box_attr}}';
				$placeholders['attr_class']        = '{{meta_box_attr_class}}';
			}
		}

		$cache_key = 'dms_meta_box_template';

		try {
			$meta_box_template = DMS_Cache::get($cache_key);
		} catch (DMS_Cache_Not_Found_Exception $e) {
			$temp_screen_id = 'dms-temp-meta-box-screen-id-' . dms_unique_increment();
			$context        = 'normal';

			add_meta_box(
				$placeholders['id'],
				$placeholders['title'],
				array($this, 'render_meta_box'),
				$temp_screen_id,
				$context,
				'default',
				$placeholders['content']
			);

			ob_start();

			do_meta_boxes($temp_screen_id, $context, null);

			$meta_box_template = ob_get_clean();

			remove_meta_box($id, $temp_screen_id, $context);

			// remove wrapper div, leave only meta box div
			{
				// <div ...>
				{
					$meta_box_template = str_replace(
						'<div id="' . $context . '-sortables" class="meta-box-sortables">',
						'',
						$meta_box_template
					);
				}

				// </div>
				{
					$meta_box_template = explode('</div>', $meta_box_template);
					array_pop($meta_box_template);
					$meta_box_template = implode('</div>', $meta_box_template);
				}
			}

			// add 'dms-postbox' class and some attr related placeholders
			$meta_box_template = str_replace(
				'class="postbox',
				$placeholders['attr'] . ' class="postbox dms-postbox' . $placeholders['attr_class'],
				$meta_box_template
			);

			// add html_before|after_title placeholders
			{
				$meta_box_template = str_replace(
					'<span>' . $placeholders['title'] . '</span>',


					'<small class="dms-html-before-title">' . $placeholders['html_before_title'] . '</small>' .
						'<span>' . $placeholders['title'] . '</span>' .
						'<small class="dms-html-after-title">' . $placeholders['html_after_title'] . '</small>',

					$meta_box_template
				);
			}

			DMS_Cache::set($cache_key, $meta_box_template);
		}

		// prepare attributes
		{
			$attr_class = '';
			if (isset($other['attr']['class'])) {
				$attr_class = ' ' . $other['attr']['class'];

				unset($other['attr']['class']);
			}

			unset($other['attr']['id']);
		}

		// replace placeholders with data/content
		return str_replace(
			array(
				$placeholders['id'],
				$placeholders['title'],
				$placeholders['content'],
				$placeholders['html_before_title'],
				$placeholders['html_after_title'],
				$placeholders['attr'],
				$placeholders['attr_class'],
			),
			array(
				esc_attr($id),
				$title,
				$content,
				$other['html_before_title'],
				$other['html_after_title'],
				dms_attr_to_html($other['attr']),
				esc_attr($attr_class)
			),
			$meta_box_template
		);
	}

	public function _register_option_type(DMS_Access_Key $access_key, $option_type_class, $type = null)
	{
		if ($access_key->get_key() !== 'dms_option_type') {
			trigger_error('Call denied', E_USER_ERROR);
		}

		$this->register_option_type($option_type_class, $type);
	}

	public function _register_container_type(DMS_Access_Key $access_key, $container_type_class)
	{
		if ($access_key->get_key() !== 'dms_container_type') {
			trigger_error('Call denied', E_USER_ERROR);
		}

		$this->register_container_type($container_type_class);
	}

	public function option_type($type)
	{
		static $did_options_init = false;
		if (!$did_options_init) {
			$did_options_init = true;
			do_action('dms_option_types_init');
		}

		if (isset($this->option_types[$type])) {
			if (is_string($this->option_types[$type])) {
				$this->option_types[$type] = $this->get_instance($this->option_types[$type]);
				$this->option_types[$type]->_call_init($this->get_access_key());
			}

			return $this->option_types[$type];
		} else {
			if (is_admin() && apply_filters('dms_backend_undefined_option_type_warn_user', true, $type)) {
				DMS_Flash_Messages::add(
					'dms-get-option-type-undefined-' . $type,
					sprintf(__('Undefined option type: %s', 'dms'), $type),
					'warning'
				);
			}

			if (!$this->undefined_option_type) {
				$this->undefined_option_type = new DMS_Option_Type_Undefined();
			}

			return $this->undefined_option_type;
		}
	}

	public function get_option_types()
	{
		$this->option_type('text'); // trigger init
		return array_keys($this->option_types);
	}

	public function get_container_types()
	{
		$this->container_type('box'); // trigger init
		return array_keys($this->container_types);
	}

	/**
	 * @param string $type
	 * @return DMS_Container_Type
	 */
	public function container_type($type)
	{
		static $did_containers_init = false;
		if (!$did_containers_init) {
			$did_containers_init = true;
			do_action('dms_container_types_init');
		}

		if (isset($this->container_types[$type])) {
			if (is_string($this->container_types[$type])) {
				$this->container_types[$type] = $this->get_instance($this->container_types[$type]);
				$this->container_types[$type]->_call_init($this->get_access_key());
			}

			return $this->container_types[$type];
		} else {
			if (is_admin()) {
				DMS_Flash_Messages::add(
					'dms-get-container-type-undefined-' . $type,
					sprintf(__('Undefined container type: %s', 'dms'), $type),
					'warning'
				);
			}

			if (!$this->undefined_container_type) {
				$this->undefined_container_type = new DMS_Container_Type_Undefined();
			}

			return $this->undefined_container_type;
		}
	}

	/**
	 * @param WP_Customize_Manager $wp_customize
	 * @internal
	 */
	public function _action_customize_register($wp_customize)
	{
		if (is_admin()) {
			add_action('admin_enqueue_scripts', array($this, '_action_enqueue_customizer_static'));
		}

		$this->customizer_register_options(
			$wp_customize,
			dm()->theme->get_customizer_options()
		);
	}

	/**
	 * @internal
	 */
	public function _action_enqueue_customizer_static()
	{ {
			$options_for_enqueue = array();
			$customizer_options = dm()->theme->get_customizer_options();

			dms_collect_options($options_for_enqueue, $customizer_options, array(
				'callback' => array(__CLASS__, '_callback_dms_collect_options_enqueue_static'),
			));

			unset($options_for_enqueue, $customizer_options);
		}

		wp_enqueue_script(
			'dms-backend-customizer',
			dms_get_framework_directory_uri('/static/js/backend-customizer.js'),
			array('jquery', 'dm-events', 'backbone', 'dms-backend-options'),
			dm()->manifest->get_version(),
			true
		);
		wp_localize_script(
			'dms-backend-customizer',
			'_dms_backend_customizer_localized',
			array(
				'change_timeout' => apply_filters('dms_customizer_option_change_timeout', 333),
			)
		);

		do_action('dms_admin_enqueue_scripts:customizer');
	}


	private function customizer_register_options($wp_customize, $options, $parent_data = array())
	{
		$collected = array();

		dms_collect_options($collected, $options, array(
			'limit_option_types' => false,
			'limit_container_types' => false,
			'limit_level' => 1,
			'info_wrapper' => true,
		));

		if (empty($collected)) {
			return;
		}

		foreach ($collected as &$opt) {
			switch ($opt['group']) {
				case 'container':
					// Check if has container options
					{
						$_collected = array();

						dms_collect_options($_collected, $opt['option']['options'], array(
							'limit_option_types' => array(),
							'limit_container_types' => false,
							'limit_level' => 1,
							'limit' => 1,
							'info_wrapper' => false,
						));

						$has_containers = !empty($_collected);

						unset($_collected);
					}

					$children_data = array(
						'group' => 'container',
						'id' => $opt['id']
					);

					$args = array(
						'title' => empty($opt['option']['title'])
							? dms_id_to_title($opt['id'])
							: $opt['option']['title'],
						'description' => empty($opt['option']['desc'])
							? ''
							: $opt['option']['desc'],
					);

					if (isset($opt['option']['wp-customizer-args']) && is_array($opt['option']['wp-customizer-args'])) {
						$args = array_merge($opt['option']['wp-customizer-args'], $args);
					}

					if ($has_containers) {
						if ($parent_data) {
							trigger_error($opt['id'] . ' panel can\'t have a parent (' . $parent_data['id'] . ')', E_USER_WARNING);
							break;
						}

						$wp_customize->add_panel($opt['id'], $args);

						$children_data['customizer_type'] = 'panel';
					} else {
						if ($parent_data) {
							if ($parent_data['customizer_type'] === 'panel') {
								$args['panel'] = $parent_data['id'];
							} else {
								trigger_error($opt['id'] . ' section can have only panel parent (' . $parent_data['id'] . ')', E_USER_WARNING);
								break;
							}
						}

						$wp_customize->add_section($opt['id'], $args);

						$children_data['customizer_type'] = 'section';
					}

					$this->customizer_register_options(
						$wp_customize,
						$opt['option']['options'],
						$children_data
					);

					unset($children_data);
					break;
				case 'option':
					$setting_id = $this->get_options_name_attr_prefix() . '[' . $opt['id'] . ']'; {
						$args_control = array(
							'label' => empty($opt['option']['label'])
								? dms_id_to_title($opt['id'])
								: $opt['option']['label'],
							'description' => empty($opt['option']['desc'])
								? ''
								: $opt['option']['desc'],
							'settings' => $setting_id,
						);

						if (isset($opt['option']['wp-customizer-args']) && is_array($opt['option']['wp-customizer-args'])) {
							$args_control = array_merge($opt['option']['wp-customizer-args'], $args_control);
						}

						if ($parent_data) {
							if ($parent_data['customizer_type'] === 'section') {
								$args_control['section'] = $parent_data['id'];
							} else {
								trigger_error('Invalid control parent: ' . $parent_data['customizer_type'], E_USER_WARNING);
								break;
							}
						} else { // the option is not placed in a section, create a section automatically
							$args_control['section'] = 'dms_option_auto_section_' . $opt['id'];

							$wp_customize->add_section($args_control['section'], array(
								'title' => empty($opt['option']['label'])
									? dms_id_to_title($opt['id'])
									: $opt['option']['label'],
							));
						}
					} {
						$args_setting = array(
							'default' => dm()->backend->option_type($opt['option']['type'])->get_value_from_input($opt['option'], null),
							'dms_option' => $opt['option'],
							'dms_option_id' => $opt['id'],
						);

						if (isset($opt['option']['wp-customizer-setting-args']) && is_array($opt['option']['wp-customizer-setting-args'])) {
							$args_setting = array_merge($opt['option']['wp-customizer-setting-args'], $args_setting);
						}

						$wp_customize->add_setting(
							new _DMS_Customizer_Setting_Option(
								$wp_customize,
								$setting_id,
								$args_setting
							)
						);

						unset($args_setting);
					}

					// control must be registered after setting
					$wp_customize->add_control(
						new _DMS_Customizer_Control_Option_Wrapper(
							$wp_customize,
							$opt['id'],
							$args_control
						)
					);
					break;
				default:
					trigger_error('Unknown group: ' . $opt['group'], E_USER_WARNING);
			}
		}
	}

	public function _set_default_render_design($design = null)
	{
		if (empty($design) || !in_array($design, $this->available_render_designs)) {
			$this->default_render_design = 'default';
		} else {
			$this->default_render_design = $design;
		}
	}

	public function get_markdown_parser($fresh_instance = false)
	{
		if (!$this->markdown_parser || $fresh_instance) {
			$this->markdown_parser = new Parsedown();
		}
		return $this->markdown_parser;
	}
}
