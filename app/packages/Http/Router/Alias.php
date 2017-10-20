<?php
namespace Package\Http\Router;

use Package\Http\Router\Factory;
use RuntimeException;

class Alias {

	/**
	* @var 		$aliases
	* @access 	private
	*/
	private static $aliases=array();

	/**
	* @var 		$criteria
	* @access 	private
	*/
	private 	$criteria;

	/**
	* @param 	$method <String>
	* @access 	public
	* @return 	Object Http\Router\Alias
	*/
	public function setMethodCriteria($method='') : Alias {
		$this->criteria = $method;
		return $this;
	}

	/**
	* @param 	$factory Http\Router\Factory
	* @param 	$alias 	<String>
	* @access 	public
	* @return 	void
	*/
	public function createNewALiasFromFactory(Factory $factory, $alias='') {
		$route = $factory->getTempRoute();
		if (strlen($this->criteria) > 0) {
			Alias::$aliases[$this->criteria][$alias] = $route;
			return true;
		}
		Alias::$aliases[$alias] = $route;
	}

	/**
	* @param 	$alias <String>
	* @param 	$method <String>
	* @access 	public
	* @return 	Boolean
	*/
	public static function hasAlias($alias='', $method='') {
		$keyword = Alias::$aliases[$alias];
		if ($method !== '') {
			if (!isset(Alias::$aliases[$method])) {
				return false;
			}
			$keyword = Alias::$aliases[$method][$alias];
		}
		return (isset($keyword)) ? true : false;
	}

}