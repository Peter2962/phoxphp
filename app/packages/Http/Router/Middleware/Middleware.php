<?php
namespace Http\Router;

use Http\Router\Middleware\Interfaces\MiddlewareInterface;

abstract class Middleware implements MiddlewareInterface {

	/**
	* @access 	public
	* @return 	void
	*/
	public function __construct() {

	}

}