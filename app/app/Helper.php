<?php
use Kit\Auth\Auth;
use App\AppManager;
use Kit\Log\Logger;

##############################
# Application helper functions
##############################

if (!function_exists('htmlFile')) {
	/**
	* @param 	$file <String>
	* @return 	String
	*/
	function htmlFile($file='')
	{
		return $file . '.html';
	}
}

if (!function_exists('phpFile')) {
	/**
	* @param 	$file <String>
	* @return 	String
	*/
	function phpFile($file='')
	{
		return $file . '.php';
	}
}

if (!function_exists('app')) {
	/**
	* Returns application instance. 
	*
	* @return 	Object
	*/
	function app()
	{
		return AppManager::getInstance();
	}
}

if (!function_exists('config')) {
	/**
	* Returns an instance of App\Config.
	*
	* @param 	$config <String>
	* @return 	Object
	*/
	function config($config='')
	{
		return new App\Config($config);
	}
}

if (!function_exists('pre')) {
	/**
	* @param 	$var <Mixed>
	* @return 	void
	*/
	function pre($var='')
	{
		print '<pre>';
		print_r($var);
		print '</pre>';
	}
}

if (!function_exists('mergeAll')) {
	/**
	* @param 	$array <Array>
	* @param 	$separator <String>
	* @return 	Array
	*/
	function mergeAll(array $array = array(), $separator=" ")
	{
		$array = $array;
		$merged = [];
		$values = array_values($array);

		foreach(array_keys($array) as $iterate => $key) {
		
			$merged[] = $key.$separator.$values[$iterate];
		
		}
		
		return $merged;		
	}
}

if (!function_exists('startSession')) {
	/**
	* @return Void
	*/
	function startSession()
	{
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	}
}

if (!function_exists('basicHash')) {
	/**
	* @param 	$string <String>
	* @return 	String
	*/
	function basicHash($string='')
	{
		return md5(sha1($string));
	}
}

if (!function_exists('isArray')) {
	/**
	* @param 	$array <Array>
	* @return 	Boolean
	*/
	function isArray(array $array=array())
	{
		return isset($array) && !empty($array);
	}
}

if (!function_exists('getLogger')) {
	/**
	* @param 	$options <Array>
	* @return 	Object
	*/
	function getLogger(String $logger, Array $options=[])
	{
		return Logger::getLogger($logger, $options);
	}
}

if (!function_exists('siteUrl')) {
	/**
	* @param 	$with <String>
	* @return 	String
	*/
	function siteUrl(String $with=null) {
		return config('app')->get('app_url') . $with;
	}
}

if (!function_exists('user')) {
	/**
	* Calls and returns user method from \Kit\Auth\Auth.
	*
	* @return 	Obejct
	*/
	function user() {
		return (new Auth())->user();
	}
}

if (!function_exists('randomString')) {
	/**
	* Generates and returns random string.
	*
	* @param 	$length <Integer>
	* @access 	public
	* @return 	String
	*/
	function randomString(Int $length=30)
	{
		$key = str_shuffle(config('app')->get('random_string_key'));
		$bits = [];
		$maxLength = mb_strlen($key, '8bit') - 1;

		for ($i = 0; $i < $length; ++$i) {
			$bits[] = $key[random_int(0, $maxLength)];
		}

		return implode('', $bits);
	}
}

if (!function_exists('baseDir')) {
	/**
	* Returns base directory.
	*
	* @param 	$with <String>
	* @return 	String
	*/
	function baseDir(String $with=null)
	{
		return BASEDIR . '/' . $with;
	}
}

if (!function_exists('appDir')) {
	/**
	* Returns app directory.
	*
	* @param 	$with <String>
	* @return 	String
	*/
	function appDir(String $with=null)
	{
		return baseDir('app') . '/'. $with;
	}
}

if (!function_exists('publicDir')) {
	/**
	* Returns public directory.
	*
	* @param 	$with <String>
	* @return 	String
	*/
	function publicDir(String $with=null)
	{
		return baseDir('public' . '/' . $with);
	}
}

if (!function_exists('vendorDir')) {
	/**
	* Returns vendor directory.
	*
	* @param 	$with <String>
	* @return 	String
	*/
	function vendorDir(String $with=null)
	{
		return appDir('vendor' . '/' . $with);
	}
}