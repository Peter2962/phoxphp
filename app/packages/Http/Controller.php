<?php
/**
* @author 	Peter Taiwo
* @version 	1.0.0
* ###############################################
* This file is part of phoxphp framework.
* ###############################################
*/
namespace Package\Http;

use App\AppManager;
use Package\Http\Request;
use Package\View\Manager as ViewManager;
use Package\DependencyInjection\Injector\InjectorBridge;

abstract class Controller extends InjectorBridge {

	/**
	* @var 		$view
	* @access 	protected
	*/
	protected 	$view;

	/**
	* @var 		$app
	* @access 	protected
	*/
	protected 	$app;

	/**
	* @var 		$request
	* @access 	protected
	*/
	protected 	$request;

	/**
	* @access 	public
	* @return 	void
	*/
	public function __construct() {
		$this->app = AppManager::getInstance();
		$this->request = $this->app->load('request');
		$manager = new ViewManager();
		$this->view = $manager->getEngine();
	}

	/**
	* @param 	$param
	* @access 	public
	* @return 	Object
	*/
	public function __get($param) {
		$response = null;

		if (property_exists($this, $param)) {
			return;
		}

		$match = preg_match('/get.*[a-zA-Z0-9]/', $param, $result);
		if ($match) {
			$res = str_replace('get', '', $result[0]);
			if (class_exists($res.'Controller')) {
				$response = new $res.'Controller';
			}
		}

		return $response;
	}

	/**
	* @param 	$variable <String>
	* @param 	$value <Mixed>
	* @access 	public
	* @return 	void
	*/
	public function setVariable($variable='', $value='') {
		$this->view->setVariable($variable, $value);
	}

	/**
	* @param 	$variable <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function getVariable($variable='') {
		return $this->view->getVariable($variable);
	}

	/**
	* @param 	$template <String>
	* @param 	$layout <String>
	* @access 	public
	* @return 	void
	*/
	public function render($template='', $layout='') {
		return $this->view->render($template, $layout);
	}

}