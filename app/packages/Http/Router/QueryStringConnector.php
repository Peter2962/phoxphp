<?php
namespace Package\Http\Router;

use Package\Http\Router\{Factory, Bag};
use RuntimeException;

class QueryStringConnector {

	/**
	* @var 		$factory
	* @access 	private
	*/
	private 	$factory;

	/**
	* @var 		$connectorList
	* @access 	private
	*/
	private static $connectorList = [];

	/**
	* Constructor method accepting $factory {Http\Router\Factory} as an argument.
	*
	* @param 	$factory Http\Router\Factory
	* @access 	public
	* @return 	void
	*/
	public function __construct(Factory $factory) {
		$this->factory = $factory;
	}

	/**
	* Adds a route's query string validation rule to @param $connectorList.
	*
	* @param 	$route <String>
	* @param 	$rule 	<Boolean>
	* @access 	public
	* @throws 	RuntimeException
	* @return 	void
	*/
	public function setRuleFor($route, $rule=false) {
		if (QueryStringConnector::isQueued($route)) {
			throw new RuntimeException($this->factory->load('en_msg')->getMessage('query_string_rule_exists', ['route' => $route]));
		}

		QueryStringConnector::$connectorList[$route] = (Integer) $rule;
	}

	/**
	* Returns an array of all routes that has query string validation
	* set.
	*
	* @access 	public
	* @return 	Array
	*/
	public static function getList() {
		return QueryStringConnector::$connectorList;
	}

	/**
	* Returns a registered route's validation value.
	*
	* @param 	$route <String>
	* @access 	public
	* @return 	Integer
	*/
	public static function getValidationFor($route) {
		return (Integer) QueryStringConnector::$connectorList[$route];
	}

	/**
	* @param 	$route <String>
	* @access 	public
	* @return 	Boolean
	*/
	public static function isQueued($route='') {
		return (isset(QueryStringConnector::$connectorList[$route])) ? true : false;
	}

}