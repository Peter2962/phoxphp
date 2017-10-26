<?php
namespace Packagist\Exceptions;
use Exception;

class InvalidPackageFileException extends Exception {

	/**
	* @param 	$message <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct($message='') {
		parent::__construct($message);
	}

}