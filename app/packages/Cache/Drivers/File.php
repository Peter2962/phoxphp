<?php
namespace Package\Cache\Drivers;

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
use Package\FileSystem\File\FileManager;
use Package\Cache\Interfaces\DriverInterface;

class File implements DriverInterface
{

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
	* {@inheritDoc}
	*/
	public function getName() : String
	{
		return 'File';
	}

	/**
	* {@inheritDoc}
	*/
	public function register() : Bool
	{
		return true;
	}

	/**
	* {@inheritDoc}
	*/
	public function add($key='', $value='', $duration=60) : Bool
	{
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

		return true;
	}

	/**
	* {@inheritDoc}
	*/
	public function get($key='')
	{
		if (!$this->exists($key)) {
			return;
		}

		return $this->reader($key)->content;
	}

	/**
	* {@inheritDoc}
	*/
	public function exists($key='') : Bool
	{
		$response = false;
		$key = $this->key($key);

		$file = $this->storage($key);
		$file = new FileManager($file);

		if ($file->exists()) {
			$response = true;
		}

		return $response;
	}

	/**
	* {@inheritDoc}
	*/
	public function delete($key='') : Bool
	{
		if (!$this->exists($key)) {
			return false;
		}

		$file = new FileManager($this->storage($this->key($key)));
		$file->delete();

		return true;
	}

	/**
	* {@inheritDoc}
	*/
	public function getCreatedDate($key='')
	{
		if (!$this->exists($key)) {
			return false;
		}

		return $this->reader($key)->created_at;	
	}

	/**
	* {@inheritDoc}
	*/
	public function getExpirationDate($key='')
	{
		if (!$this->exists($key)) {
			return false;
		}

		return $this->reader($key)->expired_at;
	}

	/**
	* {@inheritDoc}
	*/
	public function hasExpired($key='') : Bool
	{
		$response = false;
		if (!$this->exists($key)) {
			return false;
		}

		$createdDate = $this->getCreatedDate($key);
		$expirationDate = $this->getExpirationDate($key);

		if (!is_int($createdDate) || !is_int($expirationDate)) {
			return false;
		}

		$cacheTime = bcadd($createdDate, $expirationDate);
		if (time() > $cacheTime) {
			$response = true;
		}

		return $response;
	}

	/**
	* {@inheritDoc}
	*/
	public function increment($key='', $value='') : Bool
	{
		return false;
	}

	/**
	* {@inheritDoc}
	*/
	public function decrement($key='', $value='') : Bool
	{
		return false;
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	String
	*/
	private function key($string='')
	{
		return sha1($string);
	}

	/**
	* @param 	$key <String>
	* @access 	private
	* @return 	String
	*/
	private function storage($key='')
	{
		return 'app/'.config('cache')->get('storage').$key;		
	}

	/**
	* @param 	$key <String>
	* @access 	private
	* @return 	Object
	*/
	private function reader($key='')
	{
		$file = new FileManager($this->storage($this->key($key)));
		$data = (Object) unserialize($file->read());
		return $data;
	}
}