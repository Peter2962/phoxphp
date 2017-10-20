<?php
namespace Package\Http\Router\Validators;

use Package\Http\Router\Factory;
use RuntimeException;
use StdClass;

class RouteCallbackTypeValidator {

	/**
	* @var 		$factory
	* @access 	private
	*/
	private 	$factory;

	/**
	* @var 		$typeObject
	* @access 	private
	*/
	private 	$typeObject;

	/**
	* @constant STRING_CALL_TYPE_A
	*/
	const 		STRING_CALL_TYPE_A = 'Primary String Call Type';

	/**
	* @constant STRING_CALL_TYPE_B
	*/
	const 		STRING_CALL_TYPE_B = 'Secondary String Call Type';

	public function __construct(Factory $factory) {
		$this->factory = $factory;
		$this->typeObject = new StdClass;
	}

	/**
	* @access 	public
	* @return 	void
	*/
	public function validate() {
		$route = $this->factory->getConfiguredRoute();
		$callback = $route['callback'];

		if (gettype($callback) == 'object') {
			$this->typeObject->type = 'object';
			$this->typeObject->callback = $callback;
			return;
		}

		if (gettype($callback) == 'string') {
			$typeOk = ($this->typePrimary($callback) == true || $this->typeSecondary($callback) == true);
			if (!$typeOk) {
				throw new RuntimeException(sprintf("Callback type not recognized. %s type provided.", gettype($callback)));
			}
		}
	}

	/**
	* @access 	public
	* @return 	Object
	*/
	public function getGeneratedCallback() : StdClass {
		return $this->typeObject;
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	Boolean
	*/
	private function typePrimary($string='') {
		if (preg_match("/(.*[a-zA-Z0-9])\.(.*[a-zA-Z0-9])$/", $string, $match)) {
			$this->typeObject->type = 'string';
			$this->typeObject->callback = $match;
			$this->typeObject->stringType = RouteCallbackTypeValidator::STRING_CALL_TYPE_A;
			return true;
		}
		return false;
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	Boolean
	*/
	private function typeSecondary($string='') {
		if (preg_match("/(.*[a-zA-Z0-9])@(.*[a-zA-Z0-9])$/", $string, $match)) {
			$this->typeObject->type = 'string';
			$this->typeObject->callback = $match;
			$this->typeObject->stringType = RouteCallbackTypeValidator::STRING_CALL_TYPE_B;
			return true;
		}
		return false;
	}

}