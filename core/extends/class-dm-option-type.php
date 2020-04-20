<?php if (!defined('DM')) die('Forbidden');

/**
 * Backend option
 */
abstract class DM_Option_Type
{
	/**
	 * @var DM_Access_Key
	 */
	private static $access_key;

	abstract public function get_type();

	protected function _enqueue_static($id, $option, $data) {}

	abstract protected function _render($id, $option, $data);

	abstract protected function _get_value_from_input($option, $input_value);

	abstract protected function _get_defaults();

	protected function _get_data_for_js($id, $option, $data = array()) {
		return array(
			'option' => $option
		);
	}

	public function get_forced_render_design() {
		return null;
	}

	private $static_enqueued = false;

	
	final public static function get_default_id_prefix()
	{
		return dm()->backend->get_options_id_attr_prefix();
	}

	
	final public static function get_default_name_prefix()
	{
		return dm()->backend->get_options_name_attr_prefix();
	}

	final public function _call_init($access_key)
	{
		if ($access_key->get_key() !== 'dm_backend') {
			trigger_error('Method call not allowed', E_USER_ERROR);
		}

		if (method_exists($this, '_init')) {
			$this->_init();
		}
	}

	public function __construct() {

	}

	public function prepare(&$id, &$option, &$data)
	{
		$data = array_merge(
			array(
				'id_prefix'   => self::get_default_id_prefix(),   // attribute id prefix
				'name_prefix' => self::get_default_name_prefix(), // attribute name prefix
			),
			$data
		);

		$defaults = $this->get_defaults();
		$merge_attr = !empty($option['attr']) && !empty($defaults['attr']);

		$option = array_merge($defaults, $option, array(
			'type' => $this->get_type()
		));

		if ($merge_attr) {
			$option['attr'] = array_merge($defaults['attr'], $option['attr']);
		}

		if (!isset($data['value'])) {
			// if no input value, use default
			$data['value'] = $option['value'];
		}

		if (!isset($option['attr'])) {
			$option['attr'] = array();
		}

		$option['attr']['name']  = $data['name_prefix'] .'['. $id .']';
		$option['attr']['id']    = $data['id_prefix'] . $id;
		$option['attr']['class'] = 'dm-option dm-option-type-'. $option['type'] .(
			isset($option['attr']['class'])
				? ' '. $option['attr']['class']
				: ''
			);
		$option['attr']['value'] = is_array($option['value']) ? '' : $option['value'];

		/**
		 * Remove some blacklisted attributes
		 * They should be added only by the render method
		 */
		{
			unset($option['attr']['type']);
			unset($option['attr']['checked']);
			unset($option['attr']['selected']);
		}
	}


	final public function render($id, $option, $data = array())
	{
		$this->prepare($id, $option, $data);

		$this->enqueue_static($id, $option, $data);

		$html_attributes = array(
			'class' => 'dm-backend-option-descriptor',
			'data-dm-option-id' => $id,
			'data-dm-option-type' => $option['type']
		);

		$data_for_js = $this->_get_data_for_js($id, $option, $data);

		if ($data_for_js) {
			$html_attributes['data-dm-for-js'] = json_encode($data_for_js);
		}

		return dm_html_tag(
			'div',
			$html_attributes,
			$this->_render( $id, $this->load_callbacks( $option ), $data )
		);
	}

	final public function enqueue_static($id = '', $option = array(), $data = array())
	{
		if ($this->static_enqueued) {
			return false;
		}

		if (
			!doing_action('admin_enqueue_scripts')
			&&
			!did_action('admin_enqueue_scripts')
		) {
		
			return;
		}

		{
			static $option_types_static_enqueued = false;

			if (!$option_types_static_enqueued) {
				wp_enqueue_style(
					'dm-option-types',
					dm_get_framework_directory_uri('/static/css/option-types.css'),
					array('dm', 'qtip'),
					dm()->manifest->get_version()
				);
				wp_enqueue_script(
					'dm-option-types',
					dm_get_framework_directory_uri('/static/js/option-types.js'),
					array('dm-events', 'qtip', 'dm-reactive-options'),
					dm()->manifest->get_version(),
					true
				);

				$option_types_static_enqueued = true;
			}
		}

		$this->prepare($id, $option, $data);

		$call_next_time = $this->_enqueue_static($id, $option, $data);

		$this->static_enqueued = !$call_next_time;

		return $call_next_time;
	}

	
	final public function get_value_from_input($option, $input_value)
	{
		$option = array_merge(
			$this->get_defaults(),
			$option,
			array(
				'type' => $this->get_type()
			)
		);

		return $this->_get_value_from_input( $this->load_callbacks( $option ), $input_value);
	}

	
	final public function get_defaults($key = null)
	{
		$option = $this->_get_defaults();

		$option['type'] = $this->get_type();

		if (!array_key_exists('value', $option)) {
			DM_Flash_Messages::add(
				'dm-option-type-no-default-value',
				sprintf(__('Option type %s has no default value', 'dm'), $this->get_type()),
				'warning'
			);

			$option['value'] = array();
		}

		return is_string($key) ? dm_akg($key, $option) : $option;
	}

	
	public function _get_backend_width_type()
	{
		return 'fixed';
	}

	public function _default_label($id, $option) {
		return dm_id_to_title($id);
	}

	/**
	 * Use this method to register a new option type
	 *
	 * @param string|DM_Option_Type $option_type_class
	 */
	final public static function register( $option_type_class, $type = null ) {
		dm()->backend->_register_option_type( self::get_access_key(), $option_type_class, $type );
	}

	final public function storage_load($id, array $option, $value, array $params = array()) {
		if ( // do not check !empty($option['dm-storage']) because this param can be set in option defaults
			$this->get_type() === $option['type']
			&&
			($option = array_merge($this->get_defaults(), $option))
		) {
			if (is_null($value)) {
				$value = dm()->backend->option_type($option['type'])->get_value_from_input($option, $value);
			}

			return $this->_storage_load($id, $option, $value, $params);
		} else {
			return $value;
		}
	}


	protected function _storage_load($id, array $option, $value, array $params) {
		return dm_db_option_storage_load($id, $option, $value, $params);
	}


	final public function storage_save($id, array $option, $value, array $params = array()) {
		if ( // do not check !empty($option['dm-storage']) because this param can be set in option defaults
			$this->get_type() === $option['type']
			&&
			($option = array_merge($this->get_defaults(), $option))
		) {
			return $this->_storage_save($id, $option, $value, $params);
		} else {
			return $value;
		}
	}

	protected function _storage_save($id, array $option, $value, array $params) {
		return dm_db_option_storage_save($id, $option, $value, $params);
	}

	private static function get_access_key() {
		if ( self::$access_key === null ) {
			self::$access_key = new DM_Access_Key( 'dm_option_type' );
		}

		return self::$access_key;
	}

	protected function load_callbacks( $data ) {
		if ( ! is_array( $data ) ) {
			return $data;
		}

		return array_map( 'dm_call', $data );
	}
}
