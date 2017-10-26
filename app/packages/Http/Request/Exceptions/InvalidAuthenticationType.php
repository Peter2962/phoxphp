<?php
namespace Package\Http\Request\Exceptions;

use Exception;

class InvalidAuthenticationTypeException extends Exception {

	/**
	* @param 	$message String
	* @access 	public
	* @return 	void
	*/
	public function __construct($message='') {
		parent::__construct($message);
	}

}