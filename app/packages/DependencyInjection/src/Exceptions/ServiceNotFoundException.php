<?php
namespace Package\DependencyInjection\Exceptions;

use Exception;

class ServiceNotFoundException extends Exception {

	/**
	* @param 	$message <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct($message='') {
		parent::__construct($message);
	}

}