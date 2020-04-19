<?php if (!defined('DM')) die('Forbidden');

abstract class DM_Manifest
{
	/**
	 * @var array
	 */
	protected $manifest;

	
	private $not_met_requirement;

	
	private $not_met_is_final = false;

	
	private $requirements_for_verification;

	private $requirements_verification_never_called = true;

	/**
	 * @param array $manifest
	 */
	protected function __construct(array $manifest)
	{
		$manifest = array_merge(array(
			'name'        => null, // title
			'uri'         => null,
			'description' => null,
			'version'     => '0.0.0',
			'author'      => null,
			'author_uri'  => null,

		
			'requirements' => array(),
		), $manifest);

		
		{
			$requirements = $manifest['requirements'];

			$manifest['requirements'] = array();

			foreach ($this->get_default_requirements() as $default_requirement => $default_requirements) {
				$manifest['requirements'][ $default_requirement ] = isset($requirements[$default_requirement])
					? array_merge(
						$default_requirements,
						$requirements[$default_requirement]
					)
					: $default_requirements;
			}

			unset($requirements);
		}

		$this->requirements_for_verification = $manifest['requirements'];

		$this->manifest = $manifest;
	}

	/**
	 * @return array { 'requirement' => array('min_version' => '..', 'max_version' => '..') }
	 */
	abstract protected function get_default_requirements();

	/**
	 * @return bool
	 */
	public function requirements_met()
	{
		if ($this->not_met_is_final) {
			return false;
		}

		if ($this->requirements_verification_never_called) {
			$this->requirements_verification_never_called = false;

			$this->check_requirements();
		}

		return empty($this->requirements_for_verification) && empty($this->not_met_requirement);
	}

	/**
	 * @return bool
	 */
	public function check_requirements()
	{
		if ($this->not_met_is_final) {
			return false;
		}

		if ($this->requirements_met()) {
			return true;
		}

		$this->not_met_requirement = array();

		global $wp_version;

		foreach ($this->requirements_for_verification as $requirement => $requirements) {
			switch ($requirement) {
				case 'php':
					if ( ! function_exists( 'phpversion' ) ) {
						break;
					}
					if (
						isset($requirements['min_version'])
						&&
						version_compare(phpversion(), $requirements['min_version'], '<')
					) {
						$this->not_met_requirement = array(
							'requirement'  => $requirement,
							'requirements' => $requirements
						);
						$this->not_met_is_final = true;
						break 2;
					}

					if (
						isset($requirements['max_version'])
						&&
						version_compare(phpversion(), $requirements['max_version'], '>')
					) {
						$this->not_met_requirement = array(
							'requirement'  => $requirement,
							'requirements' => $requirements
						);
						$this->not_met_is_final = true;
						break 2;
					}

					// met
					unset($this->requirements_for_verification[$requirement]);
					break;
				case 'wordpress':
					if (
						isset($requirements['min_version'])
						&&
						version_compare($wp_version, $requirements['min_version'], '<')
					) {
						$this->not_met_requirement = array(
							'requirement'  => $requirement,
							'requirements' => $requirements
						);
						$this->not_met_is_final = true;
						break 2;
					}

					if (
						isset($requirements['max_version'])
						&&
						version_compare($wp_version, $requirements['max_version'], '>')
					) {
						$this->not_met_requirement = array(
							'requirement'  => $requirement,
							'requirements' => $requirements
						);
						$this->not_met_is_final = true;
						break 2;
					}

					// met
					unset($this->requirements_for_verification[$requirement]);
					break;
				case 'framework':
					if (
						isset($requirements['min_version'])
						&&
						version_compare(dm()->manifest->get_version(), $requirements['min_version'], '<')
					) {
						$this->not_met_requirement = array(
							'requirement'  => $requirement,
							'requirements' => $requirements
						);
						$this->not_met_is_final = true;
						break 2;
					}

					if (
						isset($requirements['max_version'])
						&&
						version_compare(dm()->manifest->get_version(), $requirements['max_version'], '>')
					) {
						$this->not_met_requirement = array(
							'requirement'  => $requirement,
							'requirements' => $requirements
						);
						$this->not_met_is_final = true;
						break 2;
					}

					// met
					unset($this->requirements_for_verification[$requirement]);
					break;
				case 'extensions':
					$extensions =& $requirements;

					foreach ($extensions as $extension => $extension_requirements) {
						$extension_instance = dm()->extensions->get($extension);

						if (!$extension_instance) {
							/**
							 * extension in requirements does not exists
							 * maybe try call this method later and maybe will exist, or it really does not exists
							 */
							$this->not_met_requirement = array(
								'requirement'  => $requirement,
								'extension'    => $extension,
								'requirements' => $extension_requirements
							);
							break 3;
						}

						if (
							isset($extension_requirements['min_version'])
							&&
							version_compare($extension_instance->manifest->get_version(), $extension_requirements['min_version'], '<')
						) {
							$this->not_met_requirement = array(
								'requirement'  => $requirement,
								'extension'    => $extension,
								'requirements' => $extension_requirements
							);
							$this->not_met_is_final = true;
							break 3;
						}

						if (
							isset($extension_requirements['max_version'])
							&&
							version_compare($extension_instance->manifest->get_version(), $extension_requirements['max_version'], '>')
						) {
							$this->not_met_requirement = array(
								'requirement'  => $requirement,
								'extension'    => $extension,
								'requirements' => $extension_requirements
							);
							$this->not_met_is_final = true;
							break 3;
						}

						// met
						unset($this->requirements_for_verification[$requirement][$extension]);
					}

					if (empty($this->requirements_for_verification[$requirement])) {
						// all extensions requirements met
						unset($this->requirements_for_verification[$requirement]);
					}
					break;
			}
		}

		return $this->requirements_met();
	}

	public function get_version()
	{
		return $this->manifest['version'];
	}

	public function get_name()
	{
		return $this->manifest['name'];
	}

	/**
	 * @param string $multi_key
	 * @param mixed $default_value
	 * @return mixed
	 */
	public function get( $multi_key, $default_value = null ) {
		return dm_akg( $multi_key, $this->manifest, $default_value );
	}

	/**
	 * Get entire manifest.
	 * @return array
	 */
	public function get_manifest() {
		return $this->manifest;
	}

	/**
	 * Call this only after check_requirements() failed
	 * @return array
	 */
	public function get_not_met_requirement()
	{
		return $this->not_met_requirement;
	}

	/**
	 * Return user friendly requirement as text
	 * Call this only after check_requirements() failed
	 * @return string
	 */
	public function get_not_met_requirement_text()
	{
		if (!$this->not_met_requirement) {
			return '';
		}

		$requirement = array();

		foreach ($this->not_met_requirement['requirements'] as $req_key => $req) {
			switch ($req_key) {
				case 'min_version':
					$requirement[] = __('minimum required version is', 'dm') .' '. $req;
					break;
				case 'max_version':
					$requirement[] = __('maximum required version is', 'dm') .' '. $req;
					break;
			}
		}

		$requirement = implode(' '. __('and', 'dm') .' ', $requirement);

		switch ($this->not_met_requirement['requirement']) {
			case 'php':
				if ( ! function_exists( 'phpversion' ) ) {
					break;
				}

				$requirement = sprintf(
					__('Current PHP version is %s, %s', 'dm'),
					phpversion(), $requirement
				);
				break;
			case 'wordpress':
				global $wp_version;

				$requirement = sprintf(
					__('Current WordPress version is %s, %s', 'dm'),
					$wp_version, $requirement
				);
				break;
			case 'framework':
				$requirement = sprintf(
					__('Current Framework version is %s, %s', 'dm'),
					dm()->manifest->get_version(), $requirement
				);
				break;
	
			default:
				$requirement = 'Unknown requirement "'. $this->not_met_requirement['requirement'] .'"';
		}

		return $requirement;
	}
}

class DM_Framework_Manifest extends DM_Manifest
{
	public function __construct(array $manifest)
	{
		if (empty($manifest['name'])) {
			$manifest['name'] = __('Framework', 'dm');
		}

		parent::__construct($manifest);
	}

	protected function get_default_requirements()
	{
		return array(
			'php' => array(
				'min_version' => '5.6',
				/*'max_version' => '10000.0.0',*/
			),
			'wordpress' => array(
				'min_version' => '5.0',
				/*'max_version' => '10000.0.0',*/
			),
		);
	}
}

class DM_Theme_Manifest extends DM_Manifest
{
	public function __construct(array $manifest)
	{
		$manifest_defaults = array(
			/**
			 * You can use this in a wp_option id,
			 * so that option value will be different on a theme with different id.
			 *
			 * fixme: default value should be get_option( 'stylesheet' ) but it can't be changed now
			 * because there can be themes that has saved Theme Settings in wp_option: 'dm_theme_settings_options:default'
			 * changing this default value will result in Theme Settings options "reset".
			 */
			'id' => 'default',
			'supported_extensions' => array(
				/*
				'extension_name' => array(),
				*/
			),
		);

		$theme = wp_get_theme();

		foreach(array(
			'name'        => 'Name',
			'uri'         => 'ThemeURI',
			'description' => 'Description',
			'version'     => 'Version',
			'author'      => 'Author',
			'author_uri'  => 'AuthorURI',
		) as $manifest_key => $stylesheet_header) {
			$header_value = trim($theme->get($stylesheet_header));

			if ( is_child_theme() && $theme->parent() ) {
				switch ($manifest_key) {
					case 'version':
					case 'uri':
					case 'author':
					case 'author_uri':
					case 'license':
						// force parent theme value
						$header_value = $theme->parent()->get($stylesheet_header);
						break;
					default:
						if (!$header_value) {
							// use parent theme value only if child theme value is empty
							$header_value = $theme->parent()->get($stylesheet_header);
						}
				}
			}

			if ($header_value) {
				$manifest_defaults[$manifest_key] = $header_value;
			}
		}

		parent::__construct(array_merge($manifest_defaults, $manifest));
	}

	protected function get_default_requirements()
	{
		return array(
			'php' => array(
				'min_version' => '5.6',
				/*'max_version' => '10000.0.0',*/
			),
			'wordpress' => array(
				'min_version' => '4.0',
				/*'max_version' => '10000.0.0',*/
			),
			'framework' => array(
				/*'min_version' => '0.0.0',
				'max_version' => '1000.0.0'*/
			),
			
		);
	}

	public function get_id()
	{
		return $this->manifest['id'];
	}
}

class DM_Extension_Manifest extends DM_Manifest
{
	public function __construct(array $manifest)
	{
		parent::__construct($manifest);

		unset($manifest);

		// unset unnecessary keys
		unset($this->manifest['id']);

		$this->manifest = array_merge(array(
			
			'display' => false,
			
			'standalone' => false,
			
			'thumbnail' => null,
		), $this->manifest);
	}

	protected function get_default_requirements()
	{
		return array(
			'php' => array(
				'min_version' => '5.6',
				/*'max_version' => '10000.0.0',*/
			),
			'wordpress' => array(
				'min_version' => '4.8',
				/*'max_version' => '10000.0.0',*/
			),
			'framework' => array(
				/*'min_version' => '0.0.0',
				'max_version' => '1000.0.0'*/
			),
			
		);
	}
	
}
