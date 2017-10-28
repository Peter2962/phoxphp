<?php
namespace Package\View\Engines\Lite\Storage;

class Filters {

	// Template filters go in here.....

	/**
	* @param 	$string <String>
	* @access 	public
	* @return 	String
	*/
	public function toLower($string) {
		return strtolower($string);
	}

	/**
	* @param 	$string <String>
	* @access 	public
	* @return 	String
	*/
	public function toUpper($string) {
		return strtoupper($string);
	}

}