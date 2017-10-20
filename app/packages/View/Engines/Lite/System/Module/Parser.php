<?php
#########################################
# This file is part of phoxphp framework.
#########################################
namespace Package\View\Engines\Lite\System\Module;

use RuntimeException;
use StdClass;
use Package\View\Engines\Lite\Factory;
use Package\View\Engines\Lite\System\Module\Bag;

class Parser {

	/**
	* @var 		$moduleFunction
	* @access 	private
	*/
	private 	$moduleFunction;

	/**
	* @var 		$factory
	* @access 	private
	*/
	private 	$factory;

	/**
	* @var 		$moduleBag
	* @access 	private
	*/
	private 	$moduleBag;

	/**
	* @var 		$parsedString
	* @access 	private
	*/
	private 	$parsedString;

	/**
	* @var 		$argumentTags
	* @access 	private
	*/
	private 	$argumentTags;

	/**
	* @param 	$factory Lite\Factory
	* @param 	$string <String>
	* @param 	$moduleFunction StdClass
	* @access 	public
	* @return 	void
	*/
	public function __construct(Factory $factory, Bag $moduleBag, StdClass $moduleFunction) {
		$this->moduleBag = $moduleBag;
		$this->moduleFunction = $moduleFunction;
		$this->factory = $factory;
	}

	/**
	* Checks if a string contains argument tags.
	* @param 	$string <String>
	* @access 	private
	* @return 	Boolean
	*/
	private function hasArgumentTags($string='') {
		$preg = preg_match_all("/\[(.*)\]/", $string, $matches);
		if (!$preg) {
			return false;
		}
		$this->argumentTags = $matches;
		return true;
	}

	/**
	* @access 	public
	* @return 	void
	*/
	public function parse() {
		$mergedArguments = array();
		$module = $this->moduleFunction->module;
		$moduleFunctionArguments = $this->moduleFunction->arguments;
		$moduleMap = (Object) $this->moduleBag->getModules(true)->$module; // Object of registered/mapped module...
		// Merging stored module arguments and the called module arguments...
		$mergedArguments = array_combine(array_map(function($argument) {
			return "[$argument]";
		}, $moduleMap->arguments), $moduleFunctionArguments);
		$body = $moduleMap->body;

		// If the module body has arguments, we will check if the arguments are defined...
		if($this->hasArgumentTags($body)) {
			array_map(function($argument) use ($mergedArguments, $module) {
				if (!isset($mergedArguments[$argument])) {
					throw new RuntimeException(sprintf("Invalid argument count in %s module", $module));
				}
			}, $this->argumentTags[0]);
		}

		if (!empty($mergedArguments)) {
			$body = str_replace(array_keys($mergedArguments), array_values($mergedArguments), $moduleMap->body);
		}

		$this->parsedString = $body;
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getParseResult() {
		return $this->parsedString;
	}

}