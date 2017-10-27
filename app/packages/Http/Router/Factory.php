<?php
namespace Package\Http\Router;

use Closure;
use App\Config;
use RuntimeException;
use Package\DependencyInjection\Injector\InjectorBridge;
use Package\Http\Router\Interfaces\{RouterInterface, Dispatchable};
use Package\Http\Router\{Alias, Builder, Bag, Dispatcher, QueryStringConnector};
use Package\Http\Router\Validators\{RouteParameterValidator, RouteCallbackTypeValidator, Bag as ValidatorsRepo};

class Factory extends InjectorBridge implements RouterInterface, Dispatchable {

	/**
	* @var 		$requestUri
	* @access 	private
	*/
	private 	$requestUri;

	/**
	* @var 		$requestMethod
	* @access 	private
	*/
	private 	$requestMethod;

	/**
	* @var 		$routerBag
	* @access 	private
	*/
	private 	$routerBag;

	/**
	* @var 		$routeBuilder
	* @access 	private
	*/
	private 	$routeBuilder;

	/**
	* @var 		$routeType
	* @access 	private
	*/
	private 	$routeType;

	/**
	* @var 		$filtered
	* @access 	private
	*/
	private 	$filtered;

	/**
	* @var 		$route
	* @access 	private
	*/
	private 	$route;

	/**
	* @var 		$routeCallback
	* @access 	private
	*/
	private 	$routeCallback;

	/**
	* @var 		$validators
	* @access 	private
	*/
	private 	$validators=[];

	/**
	* @var 		$routeValidator
	* @access 	private
	*/
	private 	$routeValidator=[];

	/**
	* @var 		$routeMethod
	* @access 	private
	*/
	private 	$routeMethod;

	########################
	# CONSTANTS
	########################
	const 		GET 	= 'get';
	const 		POST 	= 'post';
	const 		PUT 	= 'put';
	const 		DELETE 	= 'delete';
	const 		ALL 	= 'all';

	/**
	* We will be initializing our properties here...
	*
	* @access 	public
	* @return 	void
	*/
	public function __construct() {
		$this->routerBag = new Bag($this);
		$this->routeBuilder = new Builder($this);
		$this->requestUri = $_SERVER['REQUEST_URI'];
		$this->requestMethod = $_SERVER['REQUEST_METHOD'];
	}

	/**
	* Registers a get request route.
	*
	* @param 	$route <String>
	* @param 	$callback <Mixed
	* @param 	$validator <Array>
	* @access 	public
	* @return 	Object
	*/
	public function get($route=null, $callback=null, $validator=[]) : Factory {
		if ($this->requestMethod == 'GET') {
			$this->route = $route;
			$this->routeCallback = $callback;
			$this->routeValidator = $validator;
			$this->routerBag->pushRoute($this, $this->routeMethod = Factory::GET, $callback, $validator);
		}
		return $this;
	}

	/**
	* Registers a post request route
	*
	* @param 	$route <String>
	* @param 	$callback <Mixed>
	* @param 	$validator <Array>
	* @access 	public
	* @return 	Object
	*/
	public function post($route=null, $callback=null, $validator=[]) : Factory {
		if ($this->requestMethod == 'POST') {
			$this->route = $route;
			$this->routeCallback = $callback;
			$this->routeValidator = $validator;			
			$this->routerBag->pushRoute($this, $this->routeMethod = Factory::POST, $callback, $validator);
		}
		$this->route = null;
		return $this;
	}

	/**
	* Registers a put request route
	*
	* @param 	$route <String>
	* @param 	$callback <Mixed>
	* @param 	$validator <Array>
	* @access 	public
	* @return 	Object
	*/
	public function put($route=null, $callback=null, $validator=[]) : Factory {
		if ($this->requestMethod == 'PUT') {
			$this->route = $route;
			$this->routeCallback = $callback;
			$this->routeValidator = $validator;			
			$this->routerBag->pushRoute($this, $this->routeMethod = Factory::PUT, $callback, $validator);
		}
		$this->route = null;
		return $this;
	}

	/**
	* Registers a delete request route. 
	*
	* @param 	$route <String>
	* @param 	$callback <Mixed>
	* @param 	$validator <Array>
	* @access 	public
	* @return 	Object
	*/
	public function delete($route=null, $callback=null, $validator=[]) : Factory {
		if ($this->requestMethod == 'DELETE') {
			$this->route = $route;
			$this->routeCallback = $callback;
			$this->routeValidator = $validator;			
			$this->routerBag->pushRoute($this, $this->routeMethod = Factory::DELETE, $callback, $validator);
		}
		$this->route = null;
		return $this;
	}

	/**
	* Registers any request route.
	*
	* @param 	$route <String>
	* @param 	$callback <Mixed
	* @param 	$validator <Array>
	* @access 	public
	* @return 	Object
	*/
	public function default($route=null, $callback=null, $validator=[]) : Factory {
		$this->route = $route;
		$this->routeCallback = $callback;
		$this->routeValidator = $validator;		
		$this->routerBag->pushRoute($this, $this->routeMethod = Factory::ALL, $callback, $validator);
		$this->route = null;
		return $this;
	}

	/**
	* {@inheritDoc}
	*
	* @param 	$route <String>
	* @access 	public
	* @return 	Boolean
	*/
	public static function hasRoute($route='') {
		return (isset(Bag::getRoutes()['all'][$route])) ? true : false;
	}

	/**
	* Attaches registered middleware to router.
	*
	* @access 	public
	* @return 	void
	*/
	public function attachMiddleWare() {}

	/**
	* Returns the route that is passed to Http\Router\Builder.
	*
	* @access 	public
	* @return 	String
	*/
	public function getTempRoute() {
		return $this->route;
	}

	/**
	* @access 	public
	* @return 	Mixed
	*/
	public function getTempCallback() {
		return $this->routeCallback;
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getTempValidator() {
		return $this->routeValidator;
	}

	/**
	* This method returns the filtered request uri.
	* E.g: /profile/id/1/
	*
	* @param 	$toString <Boolean>
	* @access 	public
	* @return 	String|Array
	*/
	public function getRequestUri($toString=false) {
		$requestUri = explode("/", $this->requestUri);
		$requestUri = array_values(array_diff($requestUri, explode("/", $_SERVER['SCRIPT_NAME'])));

		if(true == $toString) {
			$requestUri = implode("/", $requestUri);
		}

		return $requestUri;
	}

	/**
	* @param 	$option <Boolean>
	* @access 	public
	* @return 	Object
	* @todo 	Add documentation
	*/
	public function secureRouteQueryString($option=false) : Factory {
		$option = (Integer) $option;
		$queryStringConnector = new QueryStringConnector($this);
		$queryStringConnector->setRuleFor($this->getTempRoute(), $option);
		return $this;
	}

	/**
	* {@inheritDoc}
	*
	* @access 	public
	* @return 	Array
	*/
	public function getConfiguredRoute() : Array {
		return Bag::getAccessedRoute();
	}

	/**
	* {@inheritDoc}
	*
	* @param 	$file <String>
	* @param 	$key <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function config($file=null, $key=null) {
		return $this->get($file, $key);
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getSharedRouteMethod() {
		return $this->routeMethod;
	}

	/**
	* {@inheritDoc}
	*
	* @param 	Closure $fallbackClosure
	* @access 	public
	* @return 	Object
	*/
	public function setValidatorFallback(Closure $fallbackClosure) : Factory {
		$validatorsRepo = new ValidatorsRepo();
		$validatorsRepo->pushRouteFallback($this, $fallbackClosure);
		return $this;
	}

	/**
	* @param 	$name <String>
	* @access 	public
	* @return 	Object Http\Router\Factory
	*/
	public function alias($name='') : Factory {
		if ($name == '') {
			throw new RuntimeException('Route alias cannot be empty');
		}
		$alias = new Alias();
		$alias->setMethodCriteria($this->getSharedRouteMethod())->createNewAliasFromFactory($this, $name);
		return $this;
	}

	/**
	* @param 	$appErrors <Array>
	* @access 	public
	* @return 	void
	*/
	public function run() {
		$this->routeBuilder->buildRoute($this);
		if (empty(Bag::getAccessedRoute()) && intval($this->routeBuilder->__build) !== 1) {
			if ($this->load('config')->get('router', 'throw_404_error') == true) {
				$errorException = $this->load('config')->get('router', '404_error_exception');
				throw new $errorException(sprintf("Route %s not registered.", $this->getRequestUri(true)));
			}
			return;
		}

		// Run validation on route slugs/parameters...
		$validator = new RouteParameterValidator($this);
		$validator->dispatchValidator();

		// Get parameter validation status
		$validationStatus = $validator->getValidatorEvent();
		if ($validationStatus == 1) {
			return;
		}

		$callbackTypeValidator = new RouteCallbackTypeValidator($this);
		$callbackTypeValidator->validate();

		$callback = $callbackTypeValidator->getGeneratedCallback();

		$dispatcher = new Dispatcher($this);
		return $dispatcher->dispatch($callback);

	}

}