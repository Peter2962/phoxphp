<?php
namespace Lite\Exceptions;

use Exception;

class ModuleNotFoundException extends Exception {

	/**
	* @param 	$message <String>
	* @access 	public
	* @return 	String
	*/
	public function __construct($message='') {
		parent::__construct($message);
	}

}