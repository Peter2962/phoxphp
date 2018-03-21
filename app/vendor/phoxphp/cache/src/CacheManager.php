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

namespace Kit\Cache;

use ReflectionClass;
use Kit\Cache\Configuration;
use Kit\FileSystem\Directory\DirectoryManager;
use Kit\DependencyInjection\Injector\InjectorBridge;
use Kit\Cache\Exceptions\InvalidCacheDriverException;

class CacheManager
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
	* Construct cache driver.
	*
	* @access 	public
	* @throws 	InvalidCacheDriverException
	* @return 	Mixed
	*/
	public function __construct()
	{
		$driver = $this->getConfig()->driver();
		$driver = ucfirst($driver);

		$interface = $this->getConfig()->driverContract();
		$storage = $this->getConfig()->storage();

		$driverClass = $driver;

		if (false == class_exists($driverClass)) {

			throw new InvalidCacheDriverException(sprintf('Class %s does not exist', $driver));
		
		}

		$driver = new ReflectionClass($driverClass);

		if (false == $driver->implementsInterface($interface)) {
			
			throw new InvalidCacheDriverException('Driver must implement interface');
		
		}

		if (CacheManager::isEnabled()) {

			$this->makeCacheStorage();

			$this->driver = new $driverClass($this);

			if (!true == $this->driver->register()) {

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
	public function add(String $key='', $value='', int $duration=60)
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
	public function get(String $key='')
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
	public function exists(String $key)
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
	public function delete(String $key)
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
	public function getCreatedDate(String $key='')
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
	public function getExpirationDate(String $key='')
	{
		return $this->driver->getExpirationDate($key);
	}

	/**
	* Checks if a cache has expired.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function hasExpired(String $key='')
	{
		return $this->driver->hasExpired($key);
	}	

	/**
	* @param 	$key <String>
	* @param 	$value <Integer>
	* @access 	public
	* @return 	void
	*/
	public function increment(String $key='', int $value)
	{
		return $this->driver->increment($key, $value);
	}

	/**
	* @param 	$key <String>
	* @param 	$value <Integer>
	* @access 	public
	* @return 	void
	*/
	public function decrement(String $key='', int $value)
	{
		return $this->driver->decrement($key, $value);
	}

	/**
	* Return cache configuration.
	*
	* @access 	public
	* @return 	Object
	*/
	public function getConfig()
	{
		return new Configuration();
	}

	/**
	* Checks if caching is enabled/allowed.
	*
	* @access 	public
	* @return 	Boolean
	*/
	public static function isEnabled() : Bool
	{
		$response = false;

		if (Configuration::enabled()) {
		
			$response = true;
		
		}

		return $response;
	}

	/**
	* Create storage for cache files.
	*
	* @access 	protected
	* @return 	void
	*/
	protected function makeCacheStorage()
	{

		if (class_exists('Kit\\FileSystem\\Directory\\DirectoryManager')) {

			if (!$this->storage()->exists()) {

				$this->storage()->mkdir();

			}

		}else{

			mkdir($this->getConfig()->storage());

		}
	}

	/**
	* @access 	private
	* @return 	Object
	*/
	private function storage()
	{
		return new DirectoryManager($this->getConfig()->storage());
	}

}