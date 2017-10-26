<?php
namespace Package\Cache\Driver;
/**
* @author 		Peter Taiwo
* @filesource 	{packages/Cache/Drivers/Memcache.php}
* @package 		Cache.Driver.Memcache
* @version 		1.0.0
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

* @property 	$cachePath
* @property 	$cacheAlive
* @property 	$cacheKey
* @property 	$cacheKeyValue
* @property 	$engine
* @property 	$lang
*/

use Package\Cache\Interfaces\DriverInterface;
use Package\Cache\Driver;
use Package\Cache;

class Apc implements DriverInterface {

	/**
	* @var 		$name
	* @access 	private
	*/
	private 	$name=null;

	/**
	* @var 		$cacheKeyValue
	* @access 	public	
	*/
	public 		$cacheKeyValue;

	/**
	* @var 		$engine
	* @access 	public	
	*/
	public 		$engine;

	/**
	* @var 		$lang
	* @access 	public	
	*/
	public 		$lang;

	/**
	* Returns the name of the cache adapter.
	*
	* @access 	public
	* @return 	String
	*/
	public function getName() {
		return 'Apc';
	}

	/**
	* Decieds whether to register the cache adapter or not.
	*
	* @access 	public
	* @return 	Boolean
	*/
	public function register() {
		return true;
	}

	/**
	* Adds/Creates a new cache key.
	*
	* @param 	$key <String>
	* @param 	$value <String>
	* @param 	$duration <Int>
	* @access 	public
	* @return 	void
	*/
	public function add($key='', $value='', $duration=60) {
		return apc_store($key, $value);
	}

	/**
	* Returns a cache with the specified key.
	*
	* @param 	$key <String>
	* @access 	public
	*/
	public function get($key='') {
		return apc_fetch($key);
	}

	/**
	* Checks if a cache key exists.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function exists($key='') {
		$response=false;
		if (apc_exists($key)) {
			$response=true;
		}
		return $response;
	}

	/**
	* Deletes a cache with the specified key.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	void
	*/
	public function delete($key='') {
		return apc_delete($key);
	}

	/**
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function getCreatedDate($key='') {
		return null;
	}

	/**
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function getExpirationDate($key='') {
		return null;
	}

	/**
	* @param 	$key <String>
	* @access 	public
	* @return 	void
	*/
	public function hasExpired($key='') {
		return null;
	}

	/**
	* Increments a cache value by the amount specified.
	*
	* @param 	$value
	* @access 	public
	* @return 	void
	*/
	public function increment($value='') {
		return null;
	}

	/**
	* decrements a cache value by the amount specified.
	*
	* @param 	$value
	* @access 	public
	* @return 	void
	*/
	public function decrement($value='') {
		return null;
	}			
}