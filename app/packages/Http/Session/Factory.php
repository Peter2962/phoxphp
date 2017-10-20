<?php
/**
* @author 	Peter Taiwo
* @version 	1.0.0
* ###############################################
* This file is part of phoxphp framework.
* ###############################################
*/
namespace Package\Http\Session;

use Package\Http\Session\Drivers\Interfaces\DriverInterface;
use Package\DependencyInjection\Injector\InjectorBridge;
use RuntimeException;
use ReflectionClass;
use App\AppManager;

class Factory extends InjectorBridge {

	/**
	* @var 		$interface
	* @access 	private
	*/
	private 	$interface = "Package\\Http\\Session\\Drivers\\Interfaces\\DriverInterface";

	/**
	* @var 		$driver
	* @access 	private
	*/
	private 	$driver;

	/**
	* @access 	public
	* @return 	void
	*/
	public function __construct() {
		$this->resolveDriver();
	}

	/**
	* Resolves driver by checking if it is allowed.
	*
	* @access 	protected
	* @return 	void
	*/
	protected function resolveDriver() {
		$driver = $this->getDriver();
		if (gettype($driver->register()) !== 'boolean' || boolval($this->getDriver()) !== true) {
			return;
		}
	}

	/**
	* Adds a new session to the session store.
	*
	* @param 	$key <String> Session key to be added.
	* @param 	$value <String> Session value of key to be added.
	* @access 	private
	* @return 	void
	*/
	public function create($key='', $value='', $timeout) {
		return $this->getDriver()->create($key, $value, $timeout);
	}

	/**
	* Checks if a session exists in the session store.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function exists($key='') {
		return $this->getDriver()->exists($key);
	}

	/**
	* Removes a session from the session store.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	void
	*/
	public function delete($key='') {
		return $this->getDriver()->delete($key);
	}

	/**
	* Reads a session from the session store using the session key @param $key.
	*
	* @param 	$Key <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function get($key='') {
		return $this->getDriver()->get()->offset($key);
	}

	/**
	* Reads all session data from the session store.
	*
	* @param 	$toObject <Boolean> The sessions will be read as an array but if this parameter is set
	* to true, the data will return an object.
	* @access 	public
	* @return 	Array|Object
	*/
	public function getAll($toObject=false) {
		return $this->getDriver()->get()->all($toObject);
	}

	/**
	* Returns the first session data in the session store.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public function getFirst() {
		return $this->getDriver()->get()->first();
	}

	/**
	* Returns the last session data in the session store.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public function getLast() {
		return $this->getDriver()->get()->last();
	}

	/**
	* Deletes all session from the session store.
	*
	* @access 	public
	* @return 	void
	*/
	public function deleteAll() {
		return $this->getDriver()->deleteAll();
	}

	/**
	* Deletes all sessions in the session store except for the sessions associated
	* with the keys provided @param $array.
	*
	* @param 	$array <Array>
	* @access 	public
	* @return 	void
	*/
	public function deleteAllExcept(array $array=[]) {
		return $this->getDriver()->deleteAllExcept($array);
	}

	/**
	* Returns created date of a session.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Integer
	*/
	public function getCreatedDate($key='') {
		return $this->getDriver()->getCreatedDate($key);
	}

	/**
	* Returns timeout of a session.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Integer
	*/
	public function getTimeout($key='') {
		return $this->getDriver()->getTimeout($key);
	}

	/**
	* Checks if a session has expired.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function isExpired($key='') {
		return $this->getDriver()->isExpired($key);
	}

	/**
	* @param 	$key <String>
	* @param 	$timeout <Integer>
	* @access 	public
	* @return  	void
	*/
	public function incrementTimeout($key='', $timeout=60) {
		return $this->getDriver()->incrementTimeout($key, $timeout);
	}

	/**
	* @param 	$key <String>
	* @param 	$timeout <Integer>
	* @access 	public
	* @return 	void
	*/
	public function decrementTimeout($key='', $timeout=60) {
		return $this->getDriver()->decrementTimeout($key, $timeout);
	}

	/**
	* Returns the session driver in use.
	*
	* @access 	public
	* @return 	String
	*/
	public function getDriverName() {
		return $this->driver;
	}

	/**
	* Returns the class name of the driver that is being used.
	*
	* @access 	public
	* @return 	String
	*/
	public function getClass() {
		$driver = $this->config()->driver;
		return "Http\\Session\\Driver\\$driver"."Driver";
	}

	/**
	* Returns the object of the session driver in use.
	*
	* @access 	protected
	* @return 	Object
	*/
	protected function getDriver() : DriverInterface {
		$driver = ucfirst($this->getConfiguration()->driver);
		$driver = "Package\\Http\\Session\\Drivers\\$driver"."Driver";
		
		if (class_exists($driver)) {
			$driverObject = new ReflectionClass($driver);
			if (!$driverObject->implementsInterface($this->interface)) {
				throw new RuntimeException(sprintf("Invalid session driver object. Driver must implement %s.", $this->interface));
			}

			return new $driver($this);
		}
	}

	/**
	* Returns an array of the session configuration.
	*
	* @access 	public
	* @return 	Object
	*/
	public function getConfiguration() {
		$config = $this->load('config')->get('session');
		if (gettype($config) !== 'array') {
			throw new RuntimeException(sprintf("Invalid session configuration provided. Array expected, %s given.", gettype($config)));
		}

		return (Object) $config;
	}

}