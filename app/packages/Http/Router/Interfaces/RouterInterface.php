<?php
namespace Package\Http\Router\Interfaces;

use Package\Http\Router\Factory;
use Closure;

interface RouterInterface {

	/**
	* @param 	$route <String
	* @param 	$callback <Mixed>
	* @param 	$prefix <Array>
	* @access 	public
	* @return 	void
	*/
	public function get($route=null, $callback=null, $prefix=array()) : Factory;

	/**
	* @param 	$route <String>
	* @param 	$callback <Mixed>
	* @param 	$prefix <Array>
	* @access 	public
	* @return 	void
	*/
	public function post($route=null, $callback=null, $prefix=array()) : Factory;

	/**
	* @param 	$route <String>
	* @param 	$callback <Mixed>
	* @param 	$prefix <Array>
	* @access 	public
	* @return 	void
	*/
	public function put($route=null, $callback=null, $prefix=array()) : Factory;

	/**
	* @param 	$route <String>
	* @param 	$callback <Mixed>
	* @param 	$prefix <Array>
	* @access 	public
	* @return 	void
	*/
	public function delete($route=null, $callback=null, $prefix=array()) : Factory;

	/**
	* @param 	$route <String
	* @param 	$callback <Mixed>
	* @param 	$prefix <Array>
	* @access 	public
	* @return 	void
	*/
	public function default($route=null, $callback=null, $prefix=array()) : Factory;

	/**
	* Checks if a route is registered.
	*
	* @param 	$route <String>
	* @access 	public
	* @return 	Boolean
	*/
	public static function hasRoute($route=''); 

	/**
	* @access 	public
	* @return 	String
	*/
	public function getTempRoute();

	/**
	* @access 	public
	* @return 	Mixed
	*/
	public function getTempCallback();

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getConfiguredRoute() : Array;

	/**
	* @access 	public
	* @return 	void
	*/
	public function attachMiddleWare();

	/**
	* @param 	$toString <Boolean>
	* @access 	public
	* @return 	String|Array
	*/
	public function getRequestUri($toString=false);

	/**
	* @param 	$file <String>
	* @param 	$key <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function config($file=null, $key=null);

	/**
	* This method is used to set fallback if a parameter/slug validation fails. This method
	* accepts a closure as an argument.
	*
	* @param 	$fallbackClosure Closure
	* @access 	public
	* @return 	Object
	*/
	public function setValidatorFallback(Closure $fallbackClosure) : Factory;

	/**
	* Gives the created route a name.
	*
	* @param 	$name <String>
	* @access 	public
	* @return 	Object Http\Router\Factory
	*/
	public function alias($name='') : Factory;

	/**
	* Returns route method that is currently accessed.
	*
	* @access 	public
	* @return 	String
	*/
	public function getSharedRouteMethod();

	/**
	* @access 	public
	* @return 	void
	*/
	public function run();

}