<?php
namespace Package\View;

use Package\DependencyInjection\Injector\InjectorBridge;

class Manager extends InjectorBridge {

	/**
	* @var 		$engine
	* @access 	private
	*/
	private static $engine;

	/**
	* Gets the view engine that is being used. To change the engine that is being used, check
	* the view configuration file in /public/config/view.php.
	*
	* @access 	public
	* @return 	Object
	*/
	public function getEngine() {
		$engine = $this->load('config')->get('view', 'engine');
		return new $engine();
	}

}