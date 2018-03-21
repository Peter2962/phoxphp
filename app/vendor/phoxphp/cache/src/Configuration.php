<?php
/**
* This configuration class exists because this cache component/package was created for phoxphp framework
* and we want it to be resuable. If a config key returns false, fallback value will be used instead.
*/

namespace Kit\Cache;

class Configuration
{

	/**
	* Returns cache component driver.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public function driver()
	{
		$attr = $this->getAttr('driver');

		if (!$attr) {

			$attr = \Kit\Cache\Drivers\File::class;

		}

		return $attr;
	}

	/**
	* Returns cache component driver.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public function driverContract()
	{

		$attr = $this->getAttr('driver_interface');

		if (!$attr) {
		
			$attr = \Kit\Cache\Contract\CacheDriverContract::class;
		
		}

		return $attr;

	}

	/**
	* Returns cache duration.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public function duration()
	{
		$attr = $this->getAttr('duration');

		if (!$attr) {
			
			$attr = 60;

		}

		return $attr;
	}

	/**
	* Returns cache storage.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public function storage()
	{
		$attr = $this->getAttr('storage');

		if (!$attr) {

			$attr = 'storage';
		
		}

		return $attr;
	}

	/**
	* Checks if caching is enabled.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public static function enabled()
	{
		if (!$attr = self::getInstance()->getAttr('enabled')) {

			$attr = true;
		
		}

		return $attr;
	}

	/**
	* Resolve config and return key.
	*
	* @access 	public
	* @return 	Mixed
	*/
	protected function getAttr($key)
	{
		if (function_exists('config')) {

			return config('cache')->get($key);

		}

		if (file_exists('config.php')) {

			$config = include 'config.php';

			if (is_array($config) && isset($config[$key])) {

				return $config[$key];

			}

		}

		return false;
	}

	/**
	* Returns instance of class.
	*
	* @access 	protected
	* @return 	Object
	*/
	protected static function getInstance()
	{
		return new self();
	}

}