<?php
use Kit\Http\Router\Contracts\MiddlewareContract;

class AppMiddleware implements MiddlewareContract
{

	/**
	* {@inheritDoc}
	*/
	public function invoke($request)
	{
		//
	}

	/**
	* {@inheritDoc}
	*/	
	public function beforeInvoke()
	{
		//
	}

	/**
	* {@inheritDoc}
	*/	
	public function afterInvoke()
	{
		//
	}

	/**
	* {@inheritDoc}
	*/	
	public function register() : Bool
	{
		return true;
	}

}