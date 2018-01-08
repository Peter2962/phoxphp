<?php
use Kit\Http\Controller;

class defaultController extends Controller
{

	/**
	* @var 		$routeParams
	* @access 	public
	*/
	public 		$routeParams;

	/**
	* @access 	public
	* @return 	void
	*/
	public function index()
	{
		$cache = app()->load('cache');
		
		$this->setVariable('site_url', config('app')->get('app_url'));
		$this->render('default', 'default');
	}

}