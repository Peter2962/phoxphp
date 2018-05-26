<?php
use Kit\Http\Response;
use Kit\Http\Session\Factory;
use Kit\Http\Request\RequestManager;
use Kit\Http\Router\Contracts\ControllerFilterContract;

class AppFilter implements ControllerFilterContract
{

	/**
	* {@inheritDoc}
	*/
	public function call(RequestManager $request, Response $response, Factory $session)
	{
		//
	}


}