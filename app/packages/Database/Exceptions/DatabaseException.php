<?php
namespace Package\Database\Exceptions;

use App\BaseException;

class DatabaseException extends BaseException {

	/**
	* @var 		$template
	* @access 	protected
	*/
	protected 	$template = '404x';

	/**
	* @param 	$message String
	* @access 	public
	* @return 	void
	*/
	public function __construct($message='') {
		parent::__construct($message);
	}

}