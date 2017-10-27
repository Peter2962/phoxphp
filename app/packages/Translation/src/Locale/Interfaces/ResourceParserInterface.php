<?php
namespace Translation\Locale\Interfaces;

use Translation\Locale\ResourceParser;
use StdClass;

interface ResourceParserInterface {

	/**
	* Parses a property file of a given locale.
	*
	* @access 	public
	* @return 	Object Translation\Locale\ResourceParser
	*/
	public function parseResource() : ResourceParser;

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getResource() : Array;

}