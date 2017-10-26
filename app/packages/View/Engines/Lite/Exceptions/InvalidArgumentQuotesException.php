<?php
namespace Lite\Exceptions;

use RuntimeException;

class InvalidArgumentQuotesException extends RuntimeException {

	/**
	* @param 	$message <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct($message='') {
		parent::__construct($message);
	}

}