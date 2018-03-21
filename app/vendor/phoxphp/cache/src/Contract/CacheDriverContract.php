<?php
namespace Kit\Cache\Contract;

use Kit\Cache\CacheManager;

interface CacheDriverContract {

	/**
	* @param 	$cacheManager <Kit\Cache\CacheManager>
	* @access 	public
	* @return 	void
	*/
	public function __construct(CacheManager $cacheManager);

	/**
	* Returns name of cache driver class.
	*
	* @access 	public
	* @return 	String
	*/
	public function getName() : String;

	/**
	* Sets whether the driver should be registered or not.
	*
	* @access 	public
	* @return 	Boolean
	*/
	public function register() : Bool;

	/**
	* Adds/Creates a new cache key.
	*
	* @param 	$key <String>
	* @param 	$value <String>
	* @param 	$duration <Integer>
	* @access 	public
	* @return 	Boolean
	*/
	public function add(String $key='', $value='', int $duration=60) : Bool;

	/**
	* Returns a cache value using the provided key.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	String
	*/
	public function get(String $key='');

	/**
	* Checks if a cache key exists.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function exists(String $key='') : Bool;

	/**
	* Deletes a cache give the cache key.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Bool
	*/
	public function delete(String $key='') : Bool;

	/**
	* Returns unixtime when the cache was created.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function getCreatedDate(String $key='');

	/**
	* Returns the unixtime of the cache's expiration date.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function getExpirationDate(String $key='');

	/**
	* Checks if a cache has expired or not.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function hasExpired(String $key='') : Bool;

	/**
	* Increments a stored cache key.
	*
	* @param 	$key <String>
	* @param 	$value <Integer>
	* @access 	public
	* @return 	void
	*/
	public function increment(String $key='', int $value=1) : Bool;

	/**
	* Decrements a stored cache key.
	*
	* @param 	$key <String>
	* @param 	$value <Integer>
	* @access 	public
	* @return 	void
	*/
	public function decrement(String $key='', int $value=1) : Bool;

}