<?php
namespace Package\DependencyInjection\Exceptions;

use Exception;

class InvalidServiceCallbackTypeException extends Exception
{

	public function __construct($message='')
	{
		parent::__construct($message);
	}

}