<?php if (!defined('DM')) die('Forbidden');


final class DM_Access_Key
{
	private static $created_keys = array();

	private $key;
	
	final public function get_key()
	{
		return $this->key;
	}

	/**
	 * @param string $unique_key unique
	 */
	final public function __construct($unique_key)
	{
		if (isset(self::$created_keys[$unique_key])) {
			trigger_error('Key "'. $unique_key .'" already defined', E_USER_ERROR);
		}
		
		self::$created_keys[$unique_key] = true;
		
		$this->key = $unique_key;
	}
}
