<?php
/********************************
Configuration loader class.
********************************/
namespace App;

use App\AppManager;

class Config {

	/**
	* @var 		$app
	* @access 	protected
	*/
	protected 	$app;

	/**
	* @var 		$configPath
	* @access 	private
	*/
	private 	$configPath = 'public'.DS.'config'.DS;

	/**
	* @var 		$config
	* @access 	private
	*/
	private 	$config = '';

	/**
	* @param 	$config <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct($config='')
	{
		$this->config = $config;
	}

	/**
	* Returns either a configuration key value or an array of configuration
	* from @param $config only if it is an array.
	*
	* @param 	$config <String>
	* @param 	$key <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function get($config='', $key='')
	{
		$path = $this->configPath;
		if ($this->config !== '') {
			$key = $config;
			$config = $this->config;
		}

		if (!file_exists(AppManager::appLibExt($path.$config))) {
			return;
		}
		
		$loadedConfig = include AppManager::appLibExt($path.$config);
		if (gettype($loadedConfig) !== 'array') {
			return;
		}

		return ($loadedConfig[$key]) ?? $loadedConfig;
	}

}