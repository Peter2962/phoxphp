<?php
namespace Package\Http\Router;

use Package\Http\Router\Interfaces\RouterInterface;
use Package\Http\Router\Builder;
use StdClass;

class Bag {

	/**
	* @var 		$router
	* @access 	private
	*/
	private 	$router;

	/**
	* @var 		$registeredRoutes
	* @access 	private
	*/
	private 	$registeredRoutes = array();

	/**
	* @var 		$routes
	* @access 	private
	*/
	private static $routes;

	/**
	* @var 		$matchedRoute
	* @access 	private
	*/
	private static $matchedRoute = array();

	/**
	* @param 	$router Http\Router\Interfaces\RouterInterface
	* @access 	public
	* @return 	void
	*/
	public function __construct(RouterInterface $router) {
		$this->router = $router;
		Bag::$routes = array('get' => array(), 'post' => array(), 'put' => array(), 'delete' => array(), 'all' => array());
	}

	/**
	* @param 	$router Http\Router\Interfaces\RouterInterface
	* @param 	$method <String>
	* @param 	$callback <Mixed>
	* @param 	$validator <Array>
	* @access 	public
	* @return 	void
	*/
	public function pushRoute(RouterInterface $router, $method='', $callback='', array $validator=array()) {
		$routeObject = new StdClass;
		Bag::$routes[$method][] = $router->getTempRoute();
		Bag::$routes['all'][$router->getTempRoute()] = array('callback' => $callback, 'validator' => $validator,
			'shared_method' => $router->getSharedRouteMethod()
		);
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public static function getRoutes() : Array {
		return Bag::$routes;
	}

	/**
	* Pushes a matched route to Http\Router\Bag::$matchedRoute. Parameters are being
	* pushed using @param $parameters so as to avoid all parameters from being pushed
	* as well.
	*
	* @param 	$builder Http\Router\Builder
	* @param 	$parameters <Array>
	* @access 	public
	* @return 	void
	*/
	public function pushMatchedRoute(Builder $builder, array $parameters=[]) {
		Bag::$matchedRoute = array('route' => $builder->getRoute(),
			'callback' => $builder->getCallback(),
			'uri' => $this->router->getRequestUri(true),
			'parameters' => $parameters,
			'validator' => $builder->getValidator(),
			'shared_method' => $builder->getMethod()
		);
	}

	/**
	* Returns the matched route.
	*
	* @access 	public
	* @return 	Array
	*/
	public static function getAccessedRoute() : Array {
		return Bag::$matchedRoute;
	}

}