<?php
###############################################
# This file is part of phoxphp framework.
################################################
namespace Package\Http\Request;

class ProxyManager {

	/**
	* @var 		$proxies
	* @access 	private
	*/
	private static $proxies = [];

	/**
	* @var 		$status
	* @access 	private
	*/
	private static $status = false;

	/**
	* @var 		$defaultProxyIp
	* @access 	public
	*/
	public static $defaultProxyIp = '127.0.0.1';

	/**
	* @var 		$defaultProxyPort
	* @access 	public
	*/
	public static $defaultProxyPort = 80;

	/**
	* Constructor
	*
	* @param 	$name <String>
	* @param 	$address <String>
	* @param 	$port <String>
	* @access 	public
	* @return 	void
	*/
	public static function createProxy($name='', $address='', $port='') {
		(Array) ProxyManager::$proxies[$name] = ['address' => $address, 'port' => $port];
		(Boolean) ProxyManager::$status = true;
	}

	/**
	* @access 	public
	* @return 	Boolean
	*/
	public static function getStatus() {
		return (Boolean) ProxyManager::$status;
	}

	/**
	* @param 	$name <String>
	* @access 	public
	* @return 	Mixed
	*/
	public static function getProxy($name='') {
		$response = null;
		if (ProxyManager::exists($name)) {
			$response = ProxyManager::$proxies[$name];
		}

		return $response;
	}

	/**
	* @param 	$name <String>
	* @access 	public
	* @return 	Boolean
	*/
	public static function exists($name='') {
		$response = false;
		if (isset(ProxyManager::$proxies[$name])) {
			$response = true;
		}

		return $response;
	}

}