<?php
use Kit\Http\Controller;

use Kit\Glider\Factory;

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
		$this->setVariable('site_url', config('app')->get('app_url'));
		$this->render('default', 'default');

		pre(Factory::getQueryBuilder());
	}

}