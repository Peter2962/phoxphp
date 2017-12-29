<?php
namespace Package\DependencyInjection;

use Package\DependencyInjection\Bag as ServiceBag;
use Package\DependencyInjection\Factory;

class MethodBag
{

	/**
	* @var 		$methodList
	* @access 	private
	*/
	private static $methodList = [];

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
	* @param 	$serviceBag DependencyInjection\Bag
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
	public function pushMethodToList($serviceId)
	{
		MethodBag::$methodList[$serviceId] = $this->serviceFactory->getCurrentAction();
	}

	/**
	* @param 	$serviceId <String>
	* @access 	public
	* @return 	Boolean
	*/
	public static function getMethodById($serviceId)
	{
		return (isset(MethodBag::$methodList[$serviceId])) ? MethodBag::$methodList[$serviceId] : false;
	}

}