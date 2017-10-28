<?php
namespace Package\Database;
/**
* @author 		Peter Taiwo
* @version 		1.0.0
* @package 		Database.Engine
* @copyright 	MIT License
* Copyright (c) 2017 PhoxPHP
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
*/

use Package\Database\Factory;

abstract class Engine {

	/**
	* Checks if a provided database engine is valid for use.
	*
	* @param 	$engine String
	* @access 	public
	* @return 	Boolean
	*/
	public static function isValid($engine='') {
		$response = false;
		$engines = Engine::getAll();
		$storeEngine = [];
		foreach($engines as $storageEngines) {
			$storeEngine[] = $storageEngines->Engine;
		}

		if (in_array($engine, $storeEngine)) {
			$response = true;
		}

		return $response;
	}

	/**
	* Returns an array/object of all available database storage engines.
	*
	* @param 	$array Boolean
	* @access 	public
	* @return 	Mixed
	*/
	public static function getAll($array=false) {
		$db = new Factory;
		$db->query('SHOW ENGINES');

		$response = ($array == true) ? $db->getArray() : $db->get()->all();
		return $response;
	}
	
}