<?php
namespace Package\DependencyInjection;

class ConfigLoader
{

	/**
	* @param 	$config <String>
	* @param 	$key <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function load($config='', $key='')
	{
		$config = 'src/Config/'.$config.'.php';
		if (!file_exists($config)) {
			trigger_error(sprintf("Config file %s is not available.", $config));
			return;
		}

		$config = include $config;
		if (gettype($config) !== 'array') {
			trigger_error(sprintf("Invalid configuration type. %s expected.", gettype($config)));
			return;
		}

		return $config[$key] ?? $config;
	}

}