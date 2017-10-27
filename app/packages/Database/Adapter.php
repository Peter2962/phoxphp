<?php
namespace Package\Database;
/**
* @author 		Peter Taiwo
* @version 		1.0.0
* @package 		Database.Adapter
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

use Configuration;
use RuntimeException;
use Package\DependencyInjection\Injector\InjectorBridge;
use Package\Database\Adapter\Interfaces\AdapterInterface;

class Adapter extends InjectorBridge {

	/**
	* @var 		$dbname
	* @access 	protected
	*/
	protected 	$dbname;

	/**
	* @var 		$connection
	* @access 	protected
	*/
	protected 	$connection;

	/**
	* @var 		$adapter
	* @access 	protected
	*/
	protected 	$adapter;

	/**
	* @var 		$driverNamespace
	* @access 	protected
	*/
	protected 	$adapterNamespace = '\\Package\\Database\\Adapter\\';

	/**
	* @var 		$setErrorCondition
	* @access 	protected
	*/
	protected 	$setErrorCondition = null;

	/**
	* @var 		$setConnectorOptions
	* @access 	protected
	*/
	protected 	$setConnectorEnvironment = [];

	/**
	* @var 		$errorInfo
	* @access 	protected
	*/
	public 		$errorInfo = null;

	/**
	* @var 		$errorCode
	* @access 	public
	*/
	public 		$errorCode = null;

	/**
	* @var 		$prepareType
	* @access 	public
	*/
	public 		$prepareType = null;

	/**
	* @access 	public
	* @return 	Object
	*/
	public function adapter() {
		return new $this->adapter;
	}

	/**
	* @access 	protected
	* @return 	String
	*/
	protected function getHost() {
		return config('database')->get('db_host');
	}

	/**
	* @access 	protected
	* @return 	String
	*/
	protected function getUser() {
		return config('database')->get('db_user');
	}

	/**
	* @access 	protected
	* @return 	String
	*/
	protected function getPassword() {
		return config('database')->get('db_password');
	}

	/**
	* @access 	protected
	* @return 	String
	*/
	protected function getDbName() {
		return config('database')->get('db_name');
	}

	/**
	* @access 	private
	* @return 	String
	*/
	private function getActiveAdapter() {
		return ('' === config('database')->get('adapter')) ? 'pdo' : config('database')->get('adapter');
	}

	/**
	* Returns the connection of the database adapter that is being used.
	*
	* @method 	getConnection
	* @param 	$command Object
	* @access 	public
	*/
	public function getConnection() {
		$this->adapter = $this->getActiveAdapter();
		$this->adapter = $this->adapterNamespace.ucfirst($this->adapter).'\\'. ucfirst($this->adapter) .'Adapter';

		if (!$this->adapterIsValid()) {
			throw new RuntimeException('Selected adapter not valid.');
			exit;
		}

		$adapter = $this->adapter();
		$errors = (Object) $adapter->getErrors();
		$this->prepareType = $adapter->prepareType();

		$this->setConnectorEnvironment = $adapter->setConnectorEnvironment;
		return $adapter->getConnection();
	}

	/**
	* @return 	Boolean
	*/
	private function adapterIsValid() {
		$response = false;
		if (get_parent_class($this->adapter()) == 'Package\\Database\\Adapter') {
			$response = true;
		}
		return $response;
	}

	/**
	* Returns the adapter's insert id function.
	* @param 	$db Object
	* @access 	public
	* @return 	Integer
	*/
	public function insertId($db) {
		return $this->adapter()->insertId($db);
	}

	/**
	* Returns the adapter's number of rows function.
	* @param 	$db
	* @access 	public
	* @return 	Integer
	*/
	public function numRows($db) {
		return $this->adapter()->numRows($db);
	}

	/**
	* Returns the escape string function used by the adapter.
	* @param 	$db Object
	* @param 	$string String
	* @access 	public
	*/
	public function escapeString($db, $string) {
		return $this->adapter()->escapeString($db, $string);
	}

	/**
	* @param 	$result Object 	
	* @param 	$param String
	* @access 	public
	* @return 	Object
	*/
	public function getObject($result, $param='') {
		return $this->adapter()->getObject($result, $param);
	}

	/**
	* @param 	$result Object 	
	* @param 	$param String
	* @access 	public
	*/
	public function getArray($result, $param='') {
		return $this->adapter()->getArray($result, $param);
	}

	/**
	* @{inherit_doc}
	* @param 	$connection Object
	* @access 	public
	*/
	public function errorInfo($connection) {
		return $this->adapter()->errorInfo($connection);
	}

	/**
	* @{inherit_doc}
	* @param 	$connection Object
	* @access 	public
	*/
	public function errorCode($connection) {
		return $this->adapter()->errorCode($connection);
	}

	/**
	* @{inherit_doc}
	* @param 	$connection
	* @access 	public
	*/
	public function beginTransaction($connection, $parameter) {
		return $this->adapter()->beginTransaction($connection, $parameter);
	}

	/**
	* @{inherit_doc}
	* @param 	$connection
	* @access 	public
	*/
	public function commit($connection) 	{
		return $this->adapter()->commit($connection);
	}

	/**
	* @{inherit_doc}
	* @param 	$connection
	* @param 	$factory Database\Factory
	* @access 	public
	*/
	public function rollBack($connection, $factory) {
		return $this->adapter()->rollBack($connection, $factory);
	}

}