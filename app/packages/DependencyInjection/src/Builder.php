<?php
namespace Package\DependencyInjection;

use Package\DependencyInjection\Exceptions\InvalidServiceCallbackTypeException;
use Package\DependencyInjection\Exceptions\ServiceNotFoundException;
use Package\DependencyInjection\Exceptions\ServiceNotAllowedException;
use Package\DependencyInjection\Bag as ServiceBag;
use Package\DependencyInjection\ParameterBag;
use Package\DependencyInjection\MethodBag;
use Package\DependencyInjection\AccessBag;
use Package\DependencyInjection\Factory;
use ReflectionClass;
use StdClass;

class Builder
{

	/**
	* @var 		$factory
	* @access 	private
	*/
	private 	$factory;

	/**
	* @var 		$serviceId
	* @access 	private
	*/
	private 	$serviceId;

	/**
	* @var 		$callbackTypesList
	* @access 	private
	*/
	private 	$callbackTypesList = ['string', 'object'];

	/**
	* @param 	$factory Service\Factory
	* @param 	$serviceId <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct(Factory $factory, $serviceId)
	{
		$this->factory = $factory;
		$this->serviceId = $serviceId;
	}

	/**
	* @param 	$serviceBag Service\Bag
	* @param 	$initParameters <Array>
	* @access 	public
	* @return 	void
	*/
	public function make(ServiceBag $serviceBag, array $initParameters=array())
	{
		$serviceCallback = $serviceBag->getService($this->serviceId);
		if (!in_array(gettype($serviceCallback), $this->callbackTypesList)) {
			throw new InvalidServiceCallbackTypeException(sprintf("Service callback type {%s} not supported.", gettype($serviceCallback)));
		}

		$callback = $this->getCallbackType($serviceCallback);
		if ($callback->name == 'Closure') {
			return call_user_func($serviceCallback, $this->factory);
		}

		$referencedObject = $this->factory->getReferenceObject();
		if (!empty(AccessBag::getAccessListById($this->serviceId))) {
			array_map(function($accessObjectName) {
				if (gettype($accessObjectName) !== 'string') {
					throw new RuntimeException(sprintf("String type expected {%s} type found.", gettype($accessObjectName)));
				}
			}, AccessBag::getAccessListById($this->serviceId));

			if (!in_array($referencedObject, AccessBag::getAccessListById($this->serviceId))) {
				throw new ServiceNotAllowedException();
			}
		}

		$parameterBag = new ParameterBag($serviceBag, $this->factory);
		$parameters = $parameterBag->getParametersById($this->serviceId);

		if ($initParameters && !empty($initParameters)) {
			$parameters = $initParameters;
		}

		if (empty($parameters)) {
			$parameters = [];
		}

		if (!$this->factory->hasMethodCall($this->serviceId)) {
			return new $serviceCallback(...$parameters);
		}

		$serviceAction = MethodBag::getMethodById($this->serviceId);
		if (gettype($serviceAction) !== 'string') {
			throw new RuntimeException(sprintf("Method type {%s} not supported.", gettype($serviceAction)));
		}

		return (new $serviceCallback(...$parameters))->$serviceAction();
	}

	/**
	* @param 	$callback <Mixed>
	* @access 	private
	* @return 	Mixed
	*/
	private function getCallbackType($callback) : StdClass
	{
		$responseObject = new StdClass();
		$__reflect = new ReflectionClass($callback);
		$responseObject->name = $__reflect->name;
		return $responseObject;
	}

}