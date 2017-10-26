<?php
########################################################
# This file is part of phoxphp framework template files.
########################################################
namespace Package\View\Engines\Lite\Parsers\Interfaces;

interface ParserInterface {

	/**
	* Returns filtered result of the parsed string.
	* @param 	$string <String>
	* @access 	public
	* @return 	String
	*/
	public function apply($string=null);

}