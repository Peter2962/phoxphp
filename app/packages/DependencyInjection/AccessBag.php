<?php
namespace Package\DependencyInjection;

use Package\DependencyInjection\Factory;

class AccessBag
{

	/**
	* @var 		$factory
	* @access 	private
	*/
	private 	$factory;

	/**
	* @var 		$accessList
	* @access 	private
	*/
	public static $accessList = [];

	/**
	* @param 	$factory Service\Factory
	* @access 	public
	* @return 	void
	*/
	public function __construct(Factory $factory)
	{
		$this->factory = $factory;
	}

	/**
	* @param 	$serviceId <String>
	* @access 	public
	* @return 	void
	*/
	public function pushToAccessList($serviceId)
	{
		AccessBag::$accessList[$serviceId] = $this->factory->getAccessList();
	}

	/**
	* @param 	$serviceId <String>
	* @access 	public
	* @return 	Array
	*/
	public static function getAccessListById($serviceId)
	{
		if (isset(AccessBag::$accessList[$serviceId])) {
			return AccessBag::$accessList[$serviceId];
		}
		return [];
	} 

}