<?php
namespace Package\View\Engines\Lite\System\Module;

use Package\View\Engines\Lite\System\Module\Module;

class Builder {

	/**
	* @var 		$path
	* @access 	private
	*/
	private 	$path;

	/**
	* @var 		$module
	* @access 	private
	*/
	private 	$module;

	/**
	* @access 	public
	* @return 	void
	*/
	public function __construct(Module $module) {
		$this->module = $module;
	}

	/**
	* Builds modules from a given path.
	*
	* @param 	$path <String>
	* @access 	public
	* @return 	void
	*/
	public function buildModule($path='') {
		$this->path = $path;
	}

}