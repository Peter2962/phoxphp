<?php
namespace Lite;

use Lite\Factory;

class Formatter {

	/**
	* Checks if a string contains single quotes.
	* @param 	$string <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function hasSingleQuote($string='') {
		if (preg_match('/^(\'|\")(.*?)/s', $string)) {
			return true;
		}
		return false;
	}

}