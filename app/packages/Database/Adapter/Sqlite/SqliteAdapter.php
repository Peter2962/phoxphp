<?php
namespace Package\Database\Adapter\Sqlite;
/**
* @author 		Peter Taiwo
* @version 		1.0.0
* @package 		Database.Adapter.SqliteAdapter
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

use PDO;
use Package\Database\Adapter;
use Package\Database\Exceptions\DatabaseException;
use Package\Database\Adapter\Interfaces\AdapterInterface;

class SqliteAdapter extends Adapter implements AdapterInterface  {

	/**
	* Returns the name of database to use.
	*
	* @param 	$name <String>
	* @access 	private
	* @return 	String
	*/
	private function getLocalStorage($name='') {
		return config()->get('database','local_storage').$name.'.db';
	}

	/**
	* @access 	public
	* @return 	Object
	*/
	public function getConnection() {
		$name = $this->getDbName();
		$this->connection = new PDO('sqlite:'.$this->getLocalStorage($this->getDbName()));
		$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $this->connection;
	}

	/**
	* Returns prepare type.
	*
	* @access 	public
	* @return 	String
	*/
	public function prepareType() {
		return 'query';
	}

	/**
	* Returns an array containing the error info and error code.
	*
	* @access 	public
	* @return 	Array
	*/
	public function getErrors() : Array {
		return ['error_info' => 'errorInfo()', 'error_code' => 'errorCode()'];
	}

	/**
	* Returns an array of information about the error that occured.
	*
	* @param 	$connection <Object>
	* @access 	public
	* @return 	Array
	*/
	public function errorInfo($connection) {
		return $connection->errorInfo();
	}

	/**
	* Returns error code of the error that occured.
	*
	* @param 	$connection <Object>
	* @access 	public
	* @return 	Integer
	*/
	public function errorCode($connection) {
		return $connection->errorCode();
	}

	/**
	* Returns the last insert id from an insert query.
	*
	* @param 	$db <Object>
	* @access 	public
	*/
	public function insertId($db) {
		return $db->lastInsertId();
	}

	/**
	* Returns the number of rows from a query.
	*
	* @param 	$result <Object>
	* @access 	public
	*/
	public function numRows($result) {
		return $result->rowCount();
	}

	/**
	* Executes a given statement.
	*
	* @param 	$query <String>
	* @access 	public
	*/
	public function query($db = null, $query='') {
		return $this->getConnection()->query($query);
	}

	/**
	* Returns the escape string function used by the driver.
	*
	* @param 	$db <Object>
	* @param 	$string <String>
	* @access 	public
	* @return 	String
	*/
	public function escapeString($db, $string) {
		return $this->getConnection()->quote($string);
	}

	/**
	* Fetches a dataset result as object.
	*
	* @param 	$result <Object>
	* @param 	$param  <Mixed> Fetch object parameter, if any.
	* @access 	public
	*/
	public function getObject($result, $param='') {
		return $result->fetchAll(PDO::FETCH_OBJ);
	}

	/**
	* Fetches all dataset result.
	*
	* @param 	$result <Object> Database connection object
	* @param 	$param <Array>  Fetch object parameter in an array, if any.
	* @access 	public
	* @deprecated
	*/
	public function get($result, $param='') {
		return $result->fetchAll();
	}

	/**
	* @param 	$result <Object>
	* @param 	$param <String>
	* @access 	public
	* @return 	Array
	*/
	public function getArray($result, $param='') {
		return $result->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	* Closes a database connection.
	*
	* @param 	$db <Obj'>
	* @access 	public
	* @todo 	Close database connection.
	*/
	public function close($db) {
		return $db = null;
	}

	/**
	* Starts a transaction.
	*
	* @param 	$db <Object>
	* @param 	$param
	* @access 	public
	* @throws 	DatabaseException
	* @return 	void
	*/
	public function beginTransaction($db, $param='') {
		try {
			return $db->beginTransaction();
		}catch(\PDOException $pdoException) {
			throw new DatabaseException($pdoException->getMessage());
		}
	}

	/**
	* Commits a transaction if it has been started.
	*
	* @param 	$db <Object>
	* @access 	public
	* @throws 	DatabaseException
	* @return 	void
	*/
	public function commit($db) {
		try {
			return $db->commit();
		}catch(\PDOException $pdoException) {
			throw new DatabaseException($pdoException->getMessage());
		}
	}

	/**
	* @param 	$db <Object>
	* @param 	$factory Database\Factory
	* @access 	public
	* @throws 	DatabaseException
	* @return 	void
	*/
	public function rollBack($db, $factory) {
		try {
			return $db->rollback();
		}catch(\PDOException $pdoException) {
			throw new DatabaseException($pdoException->getMessage());
		}
	}	

	/**
	* Returns the current connection status.
	*
	* @param 	$db <Object> Database connection object.
	* @access 	public
	* @return 	Boolean
	*/
	public function stat($db) {
		try {
			return $db->getAttribute(\PDO::ATTR_CONNECTION_STATUS);
		}catch(\PDOException $pdoException) {
			throw new DatabaseException($pdoException->getMessage());
		}
	}

}