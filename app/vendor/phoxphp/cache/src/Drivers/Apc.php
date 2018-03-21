<?php
namespace Kit\Cache\Drivers;
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
*/

use Kit\Cache\CacheManager;
use Kit\Cache\Contract\CacheDriverContract;

class Apc implements CacheDriverContract
{

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
	* {@inheritDoc}
	*/	
	public function __construct(CacheManager $cacheManager)
	{
		//
	}

	/**
	* {@inheritDoc}
	*/
	public function getName() : String
	{
		return 'Apc';
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
	public function add(String $key='', $value='', int $duration=60) : Bool
	{
		return (Boolean) apc_store($key, $value);
	}

	/**
	* {@inheritDoc}
	*/
	public function get(String $key='')
	{
		return apc_fetch($key);
	}

	/**
	* {@inheritDoc}
	*/
	public function exists(String $key='') : Bool
	{
		$response = false;

		if (apc_exists($key)) {

			$response = true;
		
		}

		return $response;
	}

	/**
	* {@inheritDoc}
	*/
	public function delete(String $key='') : Bool
	{
		return apc_delete($key);
	}

	/**
	* {@inheritDoc}
	*/
	public function getCreatedDate(String $key='')
	{
		return null;
	}

	/**
	* {@inheritDoc}
	*/
	public function getExpirationDate(String $key='')
	{
		return null;
	}

	/**
	* {@inheritDoc}
	*/
	public function hasExpired(String $key='') : Bool
	{
		return $this->exists($key);
	}

	/**
	* {@inheritDoc}
	*/
	public function increment(String $key='', int $value=1) : Bool
	{
		return (Boolean) apc_inc($key, $value);
	}

	/**
	* {@inheritDoc}
	*/
	public function decrement(String $key='', int $value=1) : Bool
	{
		return (Boolean) apc_dec($key, $value);
	}			
}