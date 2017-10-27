<?php
namespace Package\DependencyInjection;

use Package\DependencyInjection\Bag as ServiceBag;
use Package\DependencyInjection\ConfigLoader;
use Package\DependencyInjection\ParameterBag;
use Package\DependencyInjection\MethodBag;
use Package\DependencyInjection\AccessBag;
use Package\DependencyInjection\Builder;
use RuntimeException;

abstract class Factory {

	/**
	* @var 		$serviceId
	* @access 	private
	*/
	private 	$serviceId;

	/**
	* @var 		$serviceResponse
	* @access 	private
	*/
	private 	$serviceResponse;

	/**
	* @var 		$serviceMethod
	* @access 	private
	*/
	private 	$serviceMethod;

	/**
	* @var 		$passedParameters
	* @access 	private
	*/
	private 	$passedParameters;

	/**
	* @var 		$accessList
	* @access 	private
	*/
	private 	$accessList;

	/**
	* @access 	public
	* @return 	void
	*/
	public function __construct() {}

	/**
	* @param 	$file <String>
	* @param 	$key <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function config($file='', $key='') {
		$config = new ConfigLoader();
		return $config->load($file, $key);
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getReferenceObject() {
		return get_class($this);
	}

	/**
	* Prepares a new service to be added to list of services.
	*
	* @param 	$serviceId <String>
	* @param 	$serviceResponse <Mixed>
	* @access 	public
	* @return 	Object
	*/
	public function register($serviceId='', $serviceResponse='') : Factory {
		$this->serviceId = $serviceId;
		$this->serviceResponse = $serviceResponse;

		(new Bag())->pushServiceIdToList($this);
		return $this;
	}

	/**
	* Sets default parameters of a service callback.
	*
	* @param 	$parameters <Mixed>
	* @access 	public
	* @return 	Object
	*/
	public function setDefaultParameters(...$parameters) : Factory {
		$this->passedParameters = $parameters;
		(new ParameterBag(new ServiceBag(), $this))->pushParameterToList($this->getServiceId());
		return $this;
	}

	/**
	* Adds a method to call from the registered service.
	*
	* @param 	$method <String>
	* @access 	public
	* @return 	Object
	*/
	public function setAction($method='') : Factory {
		if (empty($method)) {
			throw new RuntimeException(sprintf("Unable to set action for %s service.", $this->getServiceId()));
		}

		$this->serviceMethod = $method;
		(new MethodBag(new ServiceBag(), $this))->pushMethodToList($this->getServiceId());
		return $this;
	}

	/**
	* @param 	$list
	* @access 	public
	* @return 	void
	*/
	public function setAllow(...$list) : Factory {
		$this->accessList = $list;
		(new AccessBag($this))->pushToAccessList($this->getServiceId());
		return $this;
	}

	/**
	* @param 	$serviceId <String>
	* @param 	$initParameters <Array> Array of parameters
	* @access 	public
	* @return 	Mixed
	*/
	public function load($serviceId='', ...$initParameters) {
		$service = $this;
		$config = '/public/config/services.php';
		include $config;
		$builder = new Builder($this, $serviceId);
		return $builder->make(new ServiceBag(), $initParameters);
	}

	/**
	* @param 	$serviceId <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function hasMethodCall($serviceId) {
		return MethodBag::getMethodById($serviceId);
	}

	/**
	* Returns the service id that is pushed to the service bag.
	*
	* @access 	public
	* @return 	String
	*/
	public function getServiceId() {
		return $this->serviceId;
	}

	/**
	* @access 	public
	* @return 	Mixed
	*/
	public function getServiceCallback() {
		return $this->serviceResponse;
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getPassedParameters() {
		return $this->passedParameters;
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getCurrentAction() {
		return $this->serviceMethod;
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getAccessList() {
		return $this->accessList;
	}

	/**
	* Checks if a service is registered.
	*
	* @param 	$serviceId <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function isRegistered($serviceId='') {
		$this->serviceId = $serviceId;
		return (new Bag())->hasService($this);
	}

}