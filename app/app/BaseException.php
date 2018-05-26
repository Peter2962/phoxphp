<?php
/**
* @author 		Peter Taiwo
* @copyright 	MIT License
# Copyright (c) 2018 PhoxPHP
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
# SOFTWARE.
*/

namespace App;

use Exception;

abstract class BaseException extends Exception
{

	/**
	* @var 		$template
	* @access 	protected
	*/
	protected 	$template;

	/**
	* @var 		$name
	* @access 	protected
	*/
	protected 	$name;

	/**
	* @access 	public
	* @return 	void
	*/
	public function __construct()
	{
		trigger_error($this->getExceptionClass() . ': ' . $this->getMessage(), E_USER_ERROR);
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