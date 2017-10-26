<?php
###############################################
# This file is part of phoxphp framework.
################################################
namespace Package\Http\Session\Drivers\Interfaces;

interface DriverInterface {

	/**
	* Sets the option whether to make the driver available or not.
	*
	* @access 	public
	* @return 	Boolean
	*/
	public function register();

	/**
	* Creates a new session.
	*
	* @param 	$key <String>
	* @param 	$value <Mixed>
	* @param 	$duration <Integer>
	* @access 	public
	* @return 	void
	*/
	public function create($key=null, $value=null, $duration=60);

	/**
	* Checks if session exists.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function exists($key=null);

	/**
	* Deletes a session from the session store.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	void
	*/
	public function delete($key=null);

	/**
	* @access 	public
	* @return 	Mixed
	*/
	public function get();

	/**
	* Reads all session from the session store.
	*
	* @param 	$toObject <Boolean>
	* @access 	public
	* @return 	void
	*/
	public function all($toObject=false);

	/**
	* Returns the first session data from session store.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public function first();

	/**
	* Returns the last session data from session store.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public function last();

	/**
	* Returns the session data at offset given at @param $offset.
	*
	* @param 	$offset <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function offset($offset=null);

	/**
	* Deletes all session in the session store.
	*
	* @access 	public
	* @return 	void
	*/
	public function deleteAll();

	/**
	* Deletes all session in the session store with the excemption of sessions
	* provided in @param $array.
	*
	* @param 	$array <Array>
	* @access 	public
	* @return 	void
	*/
	public function deleteAllExcept(array $array=[]);

	/**
	* Returns the session configuration pulled from the factory's getConfiguration
	* method.
	*
	* @access 	public
	* @return 	Array|Object
	*/
	public function config();

	/**
	* Returns the created time of a session.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Integer
	*/
	public function getCreatedDate($key=null);

	/**
	* Returns the expiration time of a session.
	*
	* @access 	public
	* @return 	Integer
	*/
	public function getTimeout($key=null);

	/**
	* Checks if a session has expired.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function isExpired($key=null);

	/**
	* Increments a session's timeout.
	*
	* @param 	$key <String>
	* @param 	$timeout <String>
	* @access 	public
	* @return 	void
	*/
	public function incrementTimeout($key=null, $timeout=60);

	/**
	* Derements a session's timeout.
	*
	* @param 	$key <String>
	* @param 	$timeout <String>
	* @access 	public
	* @return 	void
	*/
	public function decrementTimeout($key=null, $timeout=60);

}