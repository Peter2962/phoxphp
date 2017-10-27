<?php
namespace Http\Router\Middleware\Interfaces;

interface MiddlewareInterface {

	/**
	* @access 	public
	* @return 	void
	*/
	public function beforeMiddleware();

	/**
	* @access 	public
	* @return 	void
	*/
	public function afterMiddleware();

	/**
	* Return true to make middleware available or set false
	* to make it unavailable.
	*
	* @access 	public
	* @return 	Boolean
	*/
	public function register();

}