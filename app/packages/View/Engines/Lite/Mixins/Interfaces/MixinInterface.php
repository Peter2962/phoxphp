<?php
namespace Package\View\Engines\Lite\Mixins\Interfaces;

interface MixinInterface {

	/**
	* Registers a directive when true is being returned.
	* @access 	public
	* @return 	Boolean
	*/
	public function register();

	/**
	* Returns the output of a parsed directive.
	* @access 	public
	* @return 	Array
	*/
	public function getOutput();

}