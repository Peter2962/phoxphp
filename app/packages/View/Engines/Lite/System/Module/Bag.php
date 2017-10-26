<?php
#########################################
# This file is part of phoxphp framework.
#########################################
namespace Package\View\Engines\Lite\System\Module;

use Package\View\Engines\Lite\System\Module as ViewModule;

class Bag {

	/**
	* @var 		$modules
	* @access 	private
	*/
	private static $modules;

	/**
	* @access 	public
	* @return 	void
	*/
	public function __construct() {}

	/**
	* @access 	public
	* @return 	void
	*/
	public function pushModuleToStore(ViewModule $module) {
		$name = $module->getTempName();
		$body = $module->getTempBody();
		$arguments = $module->getTempArguments();

		Bag::$modules[$name] = array('arguments' => $arguments, 'body' => htmlentities($body));
	}

	/**
	* @param 	$object <Boolean>
	* @access 	public
	* @return 	Array
	*/
	public function getModules($object=false) {
		return ($object == true) ? (Object)Bag::$modules : Bag::$modules;
	}

	/**
	* Checks if a module exists in the bag storage
	* @param 	$module <String>
	* @access 	public
	* @return 	Boolean
	*/
	public static function moduleExists($module='') {
		if (isset(Bag::$modules[$module])) {
			return true;
		}
		return false;
	}

	/**
	* @param 	$module <String>
	* @access 	public
	* @return 	void
	*/
	public function removeModule($module='') {
		if (isset($this->modules[$module])) {
			unset($this->modules[$module]);
		}
	}

}