<?php
/**
* @author 		Peter Taiwo
* @version 		1.0.0
* @package 		Cache
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

namespace Package\Cache;

use ReflectionClass;
use Package\Cache\Config;
use Package\FileSystem\Directory\DirectoryManager;
use Package\DependencyInjection\Injector\InjectorBridge;
use Package\Cache\Exceptions\InvalidCacheDriverException;

class CacheManager extends InjectorBridge
{

	/**
	* @var 		$makeService
	* @access 	protected
	*/
	protected 	$makeService = false;

	/**
	* @var 		$driver
	* @access 	private
	*/
	private 	$driver;

	/**
	* @var 		$isRegistered
	* @access 	private
	*/
	private 	$isRegistered = false;

	/**
	* Returns an instance of the cache adapter.
	*
	* @access 	public
	* @throws 	InvalidCacheDriverException
	* @return 	Object
	*/
	public function __construct()
	{
		$driver = $this->getConfig()->driver;
		$driver = ucfirst($driver);

		$namespace = $this->getConfig()->namespace;
		$interface = $this->getConfig()->driver_interface;

		$storage = $this->getConfig()->storage;
		$driverClass = $driver;

		if (false === class_exists($driverClass)) {
			throw new InvalidCacheDriverException(sprintf('Class %s does not exist', $driver));
		}

		$driver = new ReflectionClass($driverClass);

		if (false === $driver->implementsInterface($interface)) {
			throw new InvalidCacheDriverException('Driver must implement interface');
		}

		if (CacheManager::isEnabled()) {

			if (!$this->storage()->exists()) {
				$this->storage()->mkdir();
			}

			$this->driver=new $driverClass();
			if (!true === $this->driver->register()) {
				return;
			}
			
			return $this->driver;
		}

	}

	/**
	* Adds a new cache to the cache store.
	*
	* @param 	$key <String>
	* @param 	$value <String>
	* @param 	$duration <Integer>
	* @access 	public
	* @return 	void
	*/
	public function add($key='', $value='', $duration=60)
	{
		return $this->driver->add($key, $value, $duration);
	}

	/**
	* Return value of a stored cache given it's key.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	String
	*/
	public function get($key='')
	{
		return $this->driver->get($key);
	}

	/**
	* Check if a cache exists.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	void
	*/
	public function exists($key)
	{
		return $this->driver->exists($key);
	}

	/**
	* Deletes a cache given the cache key used to store the cache.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	void
	*/
	public function delete($key)
	{
		return $this->driver->delete($key);
	}

	/**
	* Returns a stored cache's created date.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Integer
	*/
	public function getCreatedDate($key='')
	{
		return $this->driver->getCreatedDate($key);
	}

	/**
	* Returns a stored cache's expiration date.
	*	
	* @param 	$key <String>
	* @access 	public
	* @return 	Integer
	*/
	public function getExpirationDate($key='')
	{
		return $this->driver->getExpirationDate($key);
	}

	/**
	* CHeck if a cache has expired.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function hasExpired($key='')
	{
		return $this->driver->hasExpired($key);
	}	

	/**
	* @param 	$value
	* @access 	public
	* @return 	void
	*/
	public function increment($value)
	{
		return $this->driver->increment($value);
	}

	/**
	* @param 	$value
	* @access 	public
	* @return 	void
	*/
	public function decrement($value)
	{
		return $this->driver->decrement($value);
	}

	/**
	* @access 	private
	* @return 	Object
	*/
	private function storage()
	{
		return new DirectoryManager('app/' . $this->getConfig()->storage);
	}

	/**
	* Return cache configuration.
	*
	* @access 	private
	* @return 	Object
	*/
	protected function getConfig()
	{
		return (Object) config()->get('cache');
	}

	/**
	* Check if cache is enabled.
	*
	* @access 	public
	* @return 	Boolean
	*/
	public static function isEnabled() : Bool
	{
		$response = false;
		if (boolval(config('cache')->get('enabled')) == true) {
			$response = true;
		}
		return $response;
	}

}