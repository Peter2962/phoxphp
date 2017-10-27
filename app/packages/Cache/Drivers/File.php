<?php
namespace Package\Cache\Driver;
/**
* @author 		Peter Taiwo
* @filesource 	{packages/Cache/Drivers/File.php}
* @package 		Cache.Driver.File
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
*/

use Package\Cache;
use Package\Cache\Driver;
use Package\FileSystem\File\FileManager;
use Package\Cache\Interfaces\DriverInterface;

class File implements DriverInterface {

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
		return 'File';
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
		if ('' == $key || $this->exists($key)) {
			return;
		}

		$key = $this->key($key);
		$store = $this->storage($key);

		$created_at = time(); // Period when cache was created
		$content = $value; // Cache content
		$expired_at = (ctype_digit($duration)) ? $duration : config('cache')->get('duration'); // Period when cache will expire(in seconds).
		$data = compact('created_at', 'expired_at', 'content');

		$file = new FileManager($store);
		if (!$file->create()) {
			throw new \RuntimeException("Unable to create cache.");
		}

		$data = serialize($data);

		$file->write($data);
	}

	/**
	* Returns a cache value using the provided specified key.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	String
	*/
	public function get($key='') {
		if (!$this->exists($key)) {
			return;
		}

		return $this->reader($key)->content;
	}

	/**
	* Checks if a cache key exists.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function exists($key='') {
		$response = false;
		$key = $this->key($key);

		$file = $this->storage($key);
		$file = new FileSystem\File($file);

		if ($file->exists()) {
			$response = true;
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
		if (!$this->exists($key)) {
			return;
		}

		$file = new FileManager($this->storage($this->key($key)));
		$file->delete();
	}

	/**
	* Returns unixtime when the cache was created.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Integer
	*/
	public function getCreatedDate($key='') {
		if (!$this->exists($key)) {
			return;
		}

		return $this->reader($key)->created_at;	
	}

	/**
	* Returns the unixtime of the cache's expiration date.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Integer
	*/
	public function getExpirationDate($key='') {
		if (!$this->exists($key)) {
			return;
		}

		return $this->reader($key)->expired_at;
	}

	/**
	* Checks if a cache has expired or not.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function hasExpired($key='') {
		$response = false;
		if (!$this->exists($key)) {
			return;
		}

		$createdDate = $this->getCreatedDate($key);
		$expirationDate = $this->getExpirationDate($key);

		if (!is_int($createdDate) || !is_int($expirationDate)) {
			return;
		}

		$cacheTime = bcadd($createdDate, $expirationDate);
		if (time() > $cacheTime) {
			$response = true;
		}

		return $response;
	}

	/**
	* @param 	<$value>
	* @access 	public
	* @return 	void
	*/
	public function increment($value='') {
		return null;
	}

	/**
	* @param 	<$value>
	* @access 	public
	* @return 	void
	*/
	public function decrement($value='') {
		return null;
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	String
	*/
	private function key($string='') {
		return sha1($string);
	}

	/**
	* @param 	$key <String>
	* @access 	private
	* @return 	String
	*/
	private function storage($key='') {
		return 'app/'.config('cache')->get('storage').$key;		
	}

	/**
	* @param 	$key <String>
	* @access 	private
	* @return 	Object
	*/
	private function reader($key='') {
		$file = new FileManager($this->storage($this->key($key)));
		$data = (Object) unserialize($file->read());
		return $data;
	}
}