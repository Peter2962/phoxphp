<?php
namespace Package\DependencyInjection;

use RuntimeException;
use ServiceNotFoundException;
use Package\DependencyInjection\Factory;

class Bag
{

	/**
	* @var 		$serviceList
	* @access 	private
	*/
	private static $serviceList = [];

	/**
	* Adds a service to list of services if it has not been added
	* already.
	*
	* @param 	$factory DependencyInjection\Factory
	* @access 	public
	* @return 	void
	*/
	public function pushServiceIdToList(Factory $factory)
	{
		if (!$this->hasService($factory)) {
			Bag::$serviceList[$factory->getServiceId()] = $factory->getServiceCallback();
		}
	}

	/**
	* @param 	$serviceId <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function hasService(Factory $factory)
	{
		return isset(Bag::$serviceList[$factory->getServiceId()]) ? true : false;
	}

	/**
	* @param 	$serviceId <String>
	* @access 	public
	* @return 	Array
	*/
	public function getService($serviceId)
	{
		if (!isset(Bag::$serviceList[$serviceId]))
			{
			throw new RuntimeException(sprintf("Service with id %s does not exist.", $serviceId));
		}

		return Bag::$serviceList[$serviceId];
	}

}