<?php
namespace Package\View\Engines\PHView\Exceptions;

use Exception;

class FileNotFoundException extends Exception
{

	/**
	* @param 	$message <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct(String $message)
	{
		parent::__construct($message);
	}

}