<?php
namespace Package\DependencyInjection;

use Package\DependencyInjection\Bag as ServiceBag;
use Package\DependencyInjection\Factory;

class ParameterBag
{

	/**
	* @var 		$serviceBag
	* @access 	private
	*/
	private 	$serviceBag;

	/**
	* @var 		$serviceFactory
	* @access 	private
	*/
	private 	$serviceFactory;

	/**
	* @var 		$parameterList
	* @access 	private
	*/
	private static $parameterList = array();

	/**
	* @param 	$serviceBag Service\Bag
	* @access 	public
	* @return 	void
	*/
	public function __construct(ServiceBag $serviceBag, Factory $serviceFactory)
	{
		$this->serviceBag = $serviceBag;
		$this->serviceFactory = $serviceFactory;
	}

	/**
	* @param 	$serviceId <String>
	* @access 	public
	* @return 	void
	*/
	public function pushParameterToList($serviceId)
	{
		ParameterBag::$parameterList[$serviceId] = $this->serviceFactory->getPassedParameters();
	}

	/**
	* @param 	$serviceId <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function getParametersById($serviceId)
	{
		if (isset(ParameterBag::$parameterList[$serviceId])) {
			return ParameterBag::$parameterList[$serviceId];
		}
		return false;
	}

}