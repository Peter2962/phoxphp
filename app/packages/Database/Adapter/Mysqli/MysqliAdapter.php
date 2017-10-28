<?php
namespace Package\Database\Adapter\Mysqli;
/**
* @author 		Peter Taiwo
* @version 		1.0.0
* @package 		Database.Adapter.MysqliAdapter
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

use mysqli;
use Package\Database\Adapter;
use Package\Database\Adapter\Interfaces\AdapterInterface;

class MysqliAdapter extends Adapter implements AdapterInterface {

	/**
	* @var 		$connection
	* @access 	public
	*/
	public 		$connection;

	/**
	* Returns the database connector object.
	* @param 	$command <Object>
	* @access 	public
	* @return 	Object
	*/
	public function getConnection() {
		$host = $this->getHost();
		$user = $this->getUser();
		$pass = $this->getPassword();
		$name = $this->getDbName();

		$this->connection = new mysqli($host, $user, $pass, $name);
		$this->setConnectorEnvironment = [
			'error_info' => $this->connection->connect_error,
			'error_code' => $this->connection->errno,
			'exception'  => 'Exception'
		];
		
		return $this->connection;
	}

	/**
	* Returns prepare type.
	* @access 	public
	*/
	public function prepareType() {
		return 'prepare:mysqli';
	}

	/**
	* Returns an array containing the error info and error code
	* @access 	public
	*/
	public function getErrors() {
		return ['error_info' => 'error', 'error_code' => 'errno'];
	}

	/**
	* Returns information about the error that occured.
	* @param 	$connection <Object>
	* @access 	public
	* @return 	Array
	*/
	public function errorInfo($connection) {
		return $connection->error;
	}

	/**
	* Returns error code of the error that occured.
	* @param 	$connection <Object>
	* @access 	public
	* @return 	Integer
	*/
	public function errorCode($connection) {
		return $connection->errno;
	}

	/**
	* Returns the last insert id.
	* @param 	$db <Object>
	* @access 	public
	* @return 	Integer
	*/
	public function insertId($db) {
		return $db->insert_id;
	}

	/**
	* Returns the number of rows from a query.
	* @param 	$result <Object>
	* @access 	public
	* @return 	Integer
	*/
	public function numRows($result) {
		return $result->num_rows;
	}

	/**
	* Returns the query method of the adapter.
	* @param 	$db <Object>
	* @param 	$query <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function query($db, $query='') {
		return $this->getConnection()->query($query);
	}

	/**
	* Returns the escape string function used by the driver.
	* @param 	$db <Object> Database connection object
	* @param 	$string <String> String to be escaped.
	* @access 	public
	* @return 	String
	*/
	public function escapeString($db, $string) {
		return $this->getConnection()->real_escape_string($string);
	}

	/**
	* Fetches a dataset result as object.
	* @param 	$result <Object> Database connection object.
	* @param 	$param <Mixed>  Get object parameter, if any.
	* @access 	public
	* @return 	Mixed
	*/
	public function getObject($result, $param='') {
		while ($resultObject = $result->fetch_object()) {
			$results[] = $resultObject;
		}
		return $results;
	}

	/**
	* @param 	$result <Object> Database result object.
	* @param 	$param
	* @access 	public
	* @return 	Array
	* @deprecated
	*/
	public function getArray($result, $param='') {
		return $result->fetch_assoc();
	}

	/**
	* Fetches all records in a dataset.
	* @param 	$db <Object> Database connection object.
	* @param 	$param <Array>  Get object parameter, if any.
	* @access 	public
	* @return 	Array
	*/
	public function get($db, $param=[]) {
		return $db->fetch_all(implode(", ",$param));
	}

	/**
	* Closes current database connection.
	*
	* @param 	$db <Object>
	* @access 	public
	* @todo 	Close database connection.
	*/
	public function close($db) {
		return $db->close();
	}

	/**
	* Starts a transaction.
	*
	* @param 	$db <Object>
	* @param 	$parameter
	* @access 	public
	* @return 	Boolean
	*/
	public function beginTransaction($db, $parameter='') {
		return $db->begin_transaction($parameter);
	}

	/**
	* Commits a transaction if it has been started.
	*
	* @param 	$db <Object>
	* @access 	public
	* @return 	Boolean
	*/
	public function commit($db) {
		return $db->commit();
	}

	/**
	* @param 	$db <Object>
	* @param 	$factory Database\Factory
	* @access 	public
	* @return 	Boolean
	*/
	public function rollBack($db, $factory) {
		return $db->rollback();
	}

	/**
	* Returns the current connection status.
	*
	* @param 	$db <Object> Database connection object.
	* @access 	public
	*/
	public function stat($db) {
		return $db->stat();
	}

}