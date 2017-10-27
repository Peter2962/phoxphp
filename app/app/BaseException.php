<?php
namespace App;

use Exception;
use ReflectionClass;
use Package\View\ArgResolver;

abstract class BaseException extends Exception
{

	/**
	* @var 		$template
	* @access 	protected
	*/
	protected 	$template;

	/**
	* We're setting the response code to 404 in case no $codevproperty is provided.
	* 
	* @var 		$code
	* @access 	protected
	*/
	protected 	$code = 404;

	/**
	* @var 		$name
	* @access 	protected
	*/
	protected 	$name;

	/**
	* @param 	$message <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct($message='')
	{
		$message = $message;
		$line = $this->getLine();
		$name = $this->getExceptionClass();
		$file = $this->getFile();
		$template = $this->template;
		$finder = new Finder();

		$response = app()->load('response');
		$devMode = config('app')->get('devMode');
		$productionMessage = config('app')->get('production_error_message');
		$response->setResponseCode($this->code);

		$templatePath = ArgResolver::getResolvedTemplatePath($finder->get('path.view.error.templates').$template);

		include $templatePath;

		exit;
	}

	/**
	* @access 	private
	* @return 	String
	*/
	private function getExceptionClass()
	{
		return get_class($this);
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	Array
	*/
	private function getArrayFromStringLine($string='')
	{
		(String) $string = $string;
		(Array) $stringLineArray = [];

		foreach(explode("\n", $string) as $linedString) {
			$stringLineArray[] = $linedString;
		}

		return $stringLineArray;
	}

	/**
	* @param 	$string <Mixed>
	* @access 	private
	* @return 	Boolean
	*/
	private function isObject($string='')
	{
		$response = false;
		if (gettype($string) == 'object') {
			$response = $string;
		}
		return $response;
	}

}