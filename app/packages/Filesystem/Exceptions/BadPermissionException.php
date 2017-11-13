<?php
namespace Package\FileSystem\Exceptions;

use App\BaseException;

class BadPermissionException extends BaseException
{

	/**
	* @var 		$template
	* @access 	protected
	*/
	protected 	$template = 'default';

	/**
	* @param 	$message <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct($message='')
	{
		parent::__construct($message);
	}

}