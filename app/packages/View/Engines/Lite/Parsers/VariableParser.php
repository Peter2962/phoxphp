<?php
#########################################################
# This file is part of phoxphp framework template files.
#########################################################
namespace Package\View\Engines\Lite\Parsers;

use RuntimeException;
use Package\View\Engines\Lite\Factory;
use Package\View\Engines\Lite\Storage\Filters;
use Package\View\Engines\Lite\Parsers\Interfaces\ParserInterface;

class VariableParser implements ParserInterface {

	/**
	* @var 		$factory
	* @access 	private
	*/
	private 	$factory;

	/**
	* @param 	$factory Lite\Factory
	* @access 	public
	* @return 	void
	*/
	public function __construct(Factory $factory) {
		$this->factory = $factory;
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	String
	*/
	private function toCleanStr($string='') {
		return str_replace(' ', '', $string);
	}

	/**
	* Returns the parsed/filtered string.
	*
	* @param 	$string <String>
	* @access 	public
	* @return 	String
	*/
	public function apply($string=null) {
		$stringLength = strlen($string);
		$bufferedString = str_split($string);
		if (in_array($this->toCleanStr($string)[0], array("'", '"'))) {
			return $string;
		}

		$cleanString = $this->toCleanStr($string);

		if ($cleanString[0] !== '$' && $cleanString[strlen($cleanString) - 1] !== ')') {
			$string = "\$".$cleanString;
		}

		// Convert dots to `this` pointer.
		if (stripos($string, '.')) {
			$string = str_replace('.', '->', $string);
		}

		return $string;
	}

}