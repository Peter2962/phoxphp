<?php
namespace Package\Cache\Interfaces;

interface DriverInterface {

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
	* @param 	$duration <Int>
	* @access 	public
	* @return 	Boolean
	*/
	public function add($key='', $value='', $duration='') : Bool;

	/**
	* Returns a cache value using the provided key.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	String
	*/
	public function get($key='');

	/**
	* Checks if a cache key exists.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function exists($key='') : Bool;

	/**
	* Deletes a cache give the cache key.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Bool
	*/
	public function delete($key='') : Bool;

	/**
	* Returns unixtime when the cache was created.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function getCreatedDate($key='');

	/**
	* Returns the unixtime of the cache's expiration date.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function getExpirationDate($key='');

	/**
	* Checks if a cache has expired or not.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function hasExpired($key='') : Bool;

	/**
	* Increments a stored cache number.
	*
	* @param 	<$value>
	* @access 	public
	* @return 	void
	*/
	public function increment($key='', $value='') : Bool;

	/**
	* Decrements a stored cache number.
	*
	* @param 	<$value>
	* @access 	public
	* @return 	void
	*/
	public function decrement($key='', $value='') : Bool;

}