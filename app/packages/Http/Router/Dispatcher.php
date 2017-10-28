<?php
namespace Package\Http\Router;

use	StdClass;
use App\Finder;
use App\AppManager;
use RuntimeException;
use	Package\Http\Router\Factory;
use	Package\Http\Router\Interfaces\Dispatchable;
use	Package\Http\Router\Interfaces\DispatcherInterface;
use Package\Http\Router\Validators\RouteCallbackTypeValidator;

class Dispatcher implements DispatcherInterface {

	/**
	* @var 		$controller
	* @access 	protected
	*/
	protected 	$controller;

	/**
	* @var 		$model
	* @access 	protected
	*/
	protected 	$model;

	/**
	* @var 		$dispatchable
	* @access 	private
	*/
	private 	$dispatchable;

	/**
	* @var 		$appErrors
	* @access 	private
	*/
	private 	$appErrors = [];

	/**
	* Construct method accepts the requires the dispatchable interface where
	* it generates the route callbacks from.  
	*
	* @param 	$dispatchable Package\Http\Router\Factory
	* @access 	public
	* @return 	void
	*/
	public function __construct(Dispatchable $dispatchable) {
		$this->dispatchable = $dispatchable;
		$this->appErrors = AppManager::getErrors();
	}

	/**
	* @param 	$callback <Object>
	* @access 	public
	* @return 	void
	*/
	public function dispatch(StdClass $callback) {
		$configuredRoute = $this->dispatchable->getConfiguredRoute();
		$parameters = $configuredRoute['parameters'];
		$keys = array_keys($configuredRoute['parameters']);

		if ($callback->type == 'string') {
			return $this->applyStringCallback($callback->callback);
		}else{
			return call_user_func_array($callback->callback,  array_map(function($parameter) use ($parameters) {
				return $parameters[$parameter];
			}, $keys));
		}

	}

	/**
	* @param 	$array <Array>
	* @access 	private
	* @return 	void
	*/
	private function applyStringCallback(array $array=[]) {
		// Dispatch......
		$controllerName = $array[1];
		$controllerModel = $controllerName.'Model';
		$controller = $this->getControllerName($controllerName);

		if (!class_exists($controller)) {
			throw new RuntimeException(sprintf("Unable to load {%s} controller.", $controllerName));
		}

		$this->controller = new $controller();

		/**
		* After creating an instance of the controller called, we'll check to see if the
		* controller has the required "routeParams" property. This property will be used to accesed
		* the parameters returned from the route.
		*/
		if (!property_exists($this->controller, 'routeParams')) {
			throw new RuntimeException(app()->load('en_msg')->getMessage('no_default_route_param', ['controller' => $controller]));
		}

		$route = $this->dispatchable->getConfiguredRoute();
		$this->controller->routeParams = $route['parameters'];
		if (class_exists($controllerModel)) {
			$this->controller->model = new $controllerModel();
		}

		$finder = new Finder;
		$action = $array[2];		

		if (!method_exists($this->controller, $action)) {
			throw new RuntimeException(sprintf("Method {%s} not found in {%s} controller", $action, $controllerName));
		}

		ob_start();
		$this->controller->$action();
		$data = ob_get_contents();
		ob_end_clean();

		$this->appErrors = AppManager::getErrors();
		if (sizeof($this->appErrors) > 0) {
			foreach($this->appErrors as $error) {
				AppManager::getInstance()->shutdown($error['number'], $error['message'], $error['file'], $error['line']);
			}
			exit;
		}
		eval("?> $data <?php ");
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	String
	*/
	private function getControllerName($string='') {
		return (String) $string.'Controller';
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	String
	*/
	private function getViewName($string='') {
		return (String) $string.'View';
	}

}