<?php
/**
* @package 	Package\View\Engines\PHView\Layout
* @author 	Peter Taiwo
*/

namespace Package\View\Engines\PHView;

use Package\View\Engines\PHView\Exceptions\FileNotFoundException;

class Config
{

	/**
	* @var 		$configs
	* @access 	protected
	* @static
	*/
	protected static $configs = [];

	/**
	* @param 	$key <String>
	* @access 	public
	* @static
	* @return 	Mixed
	*/
	public static function get(String $key)
	{
		$configFile = 'public/config/view.phview.php';
		if (!file_exists($configFile)) {
			throw new FileNotFoundException(sprintf("File %s does not exist.", $configFile));
		}

		$configFile = include $configFile;
		$mergedConfig = array_merge(Config::$configs, $configFile);
		Config::$configs = $mergedConfig;

		return Config::$configs[$key] ?? null;
	}

	/**
	* @param 	$key <String>
	* @param 	$value <Mixed>
	* @access 	public
	* @static
	*/
	public static function set(String $key, $value)
	{
		Config::$configs[$key] = $value;
	}

}