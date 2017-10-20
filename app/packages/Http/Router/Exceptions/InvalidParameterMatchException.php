<?php
namespace Package\Http\Router\Exceptions;

use App\BaseException;
use Exception;

class InvalidParameterMatchException extends BaseException {

	/**
	* @var 		$template
	* @access 	public
	*/
	public 		$template = '404x';

	/**
	* @var 		$code
	* @access 	protected
	*/
	protected 	$code = 500;

	/**
	* @param 	$message <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct($message='') {
		parent::__construct($message);
	}

}