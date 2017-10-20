<?php
namespace View\Block\Exceptions;

use App\BaseException;

class BlockNotFoundException extends BaseException {

	/**
	* @var 		$template
	* @access 	protected
	*/
	protected 	$template = '404x';

	/**
	* @param 	$message String
	* @access 	public
	*/
	public function __construct($message='') {
		parent::__construct($message);
	}
	
}