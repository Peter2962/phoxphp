<?php
namespace Common\Controllers;

use App\Controller;

class DefaultController extends Controller
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
	}

	/**
	* {@inheritDOc}
	*/
	public function registerModel()
	{
		return \Common\Models\DefaultModel::class;
	}

}