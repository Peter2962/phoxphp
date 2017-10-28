<?php
namespace Package\Database;
/**
* @author 		Peter Taiwo
* @version 		1.0.0
* @package 		Database.Factory
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

use StdClass;
use Package\Database\Adapter;
use Package\Database\Exceptions\DatabaseException;

class Factory {

	/**
	* @var 		$errorInfo
	* @access 	public
	*/
	public 		$errorInfo;

	/**
	* @var 		$errorCode
	* @access 	public
	*/
	public 		$errorCode;

	/**
	* @var 		$query
	* @access 	public
	*/
	protected 	$query = null;

	/**
	* @var 		$connection
	* @access 	protected
	*/
	protected 	$connection;

	/**
	* @var 		$table
	* @access 	public
	*/
	public 		$table = null;

	/**
	* @var 		$fields
	* @access 	public
	*/
	public 		$fields = null;

	/**
	* @var 		$parameters
	* @access 	public
	*/
	public 		$parameters = null;

	/**
	* @var 		$join
	* @access 	public
	*/
	public 		$join = null;

	/**
	* @var 		$adapter
	* @access 	public
	*/
	public 		$adapter;

	/**
	* @var 		$bindParameters
	* @access 	public
	*/
	public 		$bindParameters = [];

	/**
	* @var 		$queryResult
	* @access 	protected
	*/
	protected 	$queryResult;

	/**
	* @var 		$factoryResult
	* @access 	protected
	*/
	protected 	$factoryResult = null;

	/**
	* Initializes database connection after selecting the adapter that is to be used.
	*
	* @method 	__construct
	* @access 	public
	* @return 	Object
	*/
	public function __construct() {
		$this->adapter    = new Adapter;
		$this->connection = $this->adapter->getConnection();
		return $this;
	}

	/**
	* Returns error code of the error occured.
	*
	* @access 	public
	* @return 	Integer
	*/
	public function getErrorCode() {
		return $this->adapter->errorCode($this);
	}

	/**
	* Returns information of the error occured.
	*
	* @access 	public
	* @return 	String
	*/
	public function getErrorInfo() {
		return $this->adapter->errorInfo($this);
	}

	/**
	* Creates an insert query.
	*
	* @param 	$table <String> \$table that will store \$fields records
	* @param 	$fields <Array> \$fields to insert into the specified \$table
	* @access 	public
	*/
	public function insert($table=null, Array $fields=[]) {
		if (null !== $table && sizeof($fields) > 0) {
			
			$this->parameters = array_values($fields);
			$placeHolders = $this->createPlaceholders(array_keys($fields));

			(Array) $valueString = [];

			foreach($this->parameters as $x) {
				$valueString[] = $this->escape($x);
			}

			$this->preparedQuery = 'INSERT INTO '.$table.' ('.implode(',', array_keys($fields)).') VALUES ('.$placeHolders.')';
			$this->query = 'INSERT INTO '.$table.' ('.implode(',', array_keys($fields)).') VALUES ('.implode(',', $valueString).')';

			if ($this->adapter->prepareType == 'query') {
				$this->connection->query($this->query);

			}else if($this->adapter->prepareType == 'prepare:pdo'){
				$stmt = $this->connection->prepare($this->preparedQuery);
				$stmt->execute($this->parameters);

			}else if($this->adapter->prepareType == 'prepare:mysqli'){

				$type = '';

				foreach(array_values($this->parameters) as $iterate => $val) {
					if (is_string($val)) {
						$type .= 's';
					}

					if (is_int($val)) {
						$type .= 'i';
					}

					if (is_double($val)) {
						$type .= 'd';
					}

					$this->bindParameters[] = $type;
				}

				foreach(array_keys($this->parameters) as $iterate => $val) {
					$this->bindParameters[] =& $this->escape($this->parameters[$val]);
				}

				$stmt=$this->connection->prepare($this->preparedQuery);
				call_user_func_array([$stmt, 'bind_param'], $this->bindParameters);
				$stmt->execute();

			}

			return $this;
		}
	}

	/**
	* Creates a replace query.
	*
	* @param 	$table <String> \$table that will store \$fields records
	* @param 	$fields <Array>  \$fields to replace into the specified \$table
	* @access 	public
	*/
	public function replace($table=null, Array $fields=[]) {
		if ('' !== $table && sizeof($fields) > 0) {
			
			$this->parameters = array_values($fields);
			$placeHolders = $this->createPlaceholders(array_keys($fields));
			$valueString = [];

			foreach($this->parameters as $x) {
				$valueString[] = $this->escape($x);
			}

			$this->preparedQuery = 'REPLACE INTO '.$table.' ('.implode(',', array_keys($fields)).') VALUES ('.$placeHolders.')';
			$this->query = 'REPLACE INTO '.$table.' ('.implode(',', array_keys($fields)).') VALUES ('.implode(',', $valueString).')';
			
			if ($this->adapter->prepareType == 'query') {
				$this->connection->query($this->query);
				
			}else if($this->adapter->prepareType == 'prepare:pdo'){
				
				$stmt = $this->connection->prepare($this->preparedQuery);
				$stmt->execute($this->parameters);

			}else if($this->adapter->prepareType == 'prepare:mysqli'){
			
				$type = '';
				foreach(array_values($this->parameters) as $iterate => $val) {
					if (is_string($val)) {
						$type .= 's';
					}

					if (is_int($val)) {
						$type .= 'i';
					}

					if (is_double($val)) {
						$type .= 'd';
					}

					$this->bindParameters[] = $type;
				}

				foreach(array_keys($this->parameters) as $iterate => $val) {
					$this->bindParameters[] =& $this->escape($this->parameters[$val]);
				}				
				
				$stmt = $this->connection->prepare($this->preparedQuery);
				call_user_func_array([$stmt, 'bind_param'], $this->bindParameters);
				$stmt->execute();
			}

			return $this;
		}
	}

	/**
	* @param 	$fields <Array> \$fields to insert into the specified \$table
	* @access 	public
	* @return 	Object
	*/
	public function select(Array $fields=[]) {
		$placeHolders = $this->createPlaceholders($fields);
		$this->query = "SELECT ".implode(', ', $fields)."";
		return $this;
	}

	/**
	* @param 	$table 	<String> \$table where records will be fetched \$fields records
	* @access 	public
	* @return 	Object
	*/
	public function from($table='') {
		(String)$this->table = $table;
		$this->query .= " FROM ".$this->table;
		return $this;
	}

	/**
	* Creates a join statement on the current \$query property.
	*
	* @param 	$table <String> \$table where records will be fetched \$fields records
	* @param 	$joins <String>	
	* @access 	public
	* @return 	Object
	*/
	public function join($table='', $joins='') {
		(String)$this->query = $this->query." JOIN ".$table." ON $joins";
		return $this;
	}

	/**
	* Creates an inner join statement on the current \$query property.
	*
	* @param 	$table <String> \$table where records will be fetched \$fields records
	* @param 	$joins <String>	
	* @access 	public
	* @return 	Object
	*/
	public function innerJoin($table='', $joins='') {
		(String)$this->query = $this->query." INNER JOIN ".$table." ON $joins";
		return $this;
	}

	/**
	* Creates a left join statement on the current \$query property.
	*
	* @param 	$table <String> \$table where records will be fetched \$fields records
	* @param 	$joins <String>	
	* @access 	public
	* @return 	Object
	*/
	public function leftJoin($table='', $joins='') {
		(String)$this->query = $this->query." LEFT JOIN ".$table." ON $joins";
		return $this;
	}

	/**
	* Creates a right join statement on the current \$query property.
	*
	* @param 	$table <String>
	* @param 	$joins <String>
	* @access 	public
	* @return 	Object
	*/
	public function rightJoin($table='', $joins='') {
		(String)$this->query = $this->query." RIGHT JOIN ".$table." ON $joins";
		return $this;
	}

	/**
	* Creates a full join statement on the current \$query property.
	*
	* @param 	$table <String> \$table where records will be fetched \$fields records
	* @param 	$joins <String>	
	* @access 	public
	* @return 	Object
	*/
	public function fullJoin($table='', $joins='') {
		(String)$this->query = $this->query." FULL JOIN ".$table." ON $joins";
		return $this;
	}

	/**
	* Creates a cross join statement on the current \$query property.
	*
	* @param 	$table <String> \$table where records will be fetched \$fields records
	* @param 	$joins <String>	
	* @access 	public
	* @return 	Object
	*/
	public function crossJoin($table='', $joins='') {
		(String)$this->query = $this->query." CROSS JOIN ".$table;
		return $this;
	}	

	/**
	* Adds a limit clause to the current query string.
	*
	* @param 	$limit 	<Integer>
	* @param 	$offset <Integer>
	* @access 	public
	* @return 	Object
	*/
	public function limit($limit='', $offset='') {
		if ( is_int($limit) && !is_int($offset)) {
			$this->query = $this->query." LIMIT ".$limit;
		}else if (is_int($limit) && is_int($offset)) {
			$this->query = $this->query." LIMIT ".$limit." OFFSET ".$offset;
		}

		return $this;
	}

	/**
	* Attaches an order by clause to the query string.
	*
	* @param 	$orders <Array>
	* @access 	public
	* @return 	Object
	*/
	public function orderBy(array $orders=[]) {
		if (sizeof($orders) > 0) {
			$this->query = $this->query." ORDER BY ".implode(", ", $orders);
		}

		return $this;
	}

	/**
	* Attaches a group by clause to the query string.
	*
	* @param 	$col <Mixed>
	* @access 	public
	* @return 	Object
	*/
	public function groupBy($col='') {
		if ('' !== $col) {
			$this->query = $this->query." GROUP BY ".$col;
		}

		return $this;
	}

	/**
	* Attaches a WHERE statement to the query string
	* @param 	$where <String>
	* @access 	public
	* @return 	Object
	*/
	public function where($where='') {
		if ('' !== $where) {
			$this->query = $this->query." WHERE ".$where;
		}

		return $this;
	}

	/**
	* Attaches a WHERE statement with the AND clause to the query string.
	*
	* @param 	$where <String>
	* @access 	public
	* @return 	Object
	*/
	public function whereAnd($where='') {
		if ('' !== $where) {
			$this->query = $this->query." AND ".$where;
		}

		return $this;
	}

	/**
	* Attaches a WHERE statement with the OR clause to the query string
	* @param 	$where <String>
	* @access 	public
	* @return 	Object
	*/
	public function whereOr($where='') {
		if ('' !== $where) {
			$this->query = $this->query." OR ".$where;
		}

		return $this;
	}

	/**
	* Attaches a WHERE statement with the LIKE clause to the query string.
	*
	* @param 	$where <String>
	* @param 	$like <String>
	* @access 	public
	* @return 	Object
	*/
	public function whereLike($where='', $like='') {
		if ('' !== $where && '' !== $like) {
			$this->query = $this->query." WHERE ".$where." LIKE '%".$like."%'";
		}

		return $this;
	}

	/**
	* Attaches a WHERE statement with the NOT LIKE clause to the query string.
	*
	* @param 	$where <String>
	* @param 	$like <String>
	* @access 	public
	* @return 	Object
	*/
	public function whereNotLike($where='', $like='') {
		if ('' !== $where && '' !== $like) {
			$this->query = $this->query." WHERE ".$where." NOT LIKE '%".$like."%'";
		}

		return $this;
	}	

	/**
	* Attaches a WHERE statement with the IN clause to the query string.
	*
	* @param 	$where <String>
	* @param 	$in <Array>
	* @access 	public
	* @return 	Object
	*/
	public function whereIn($where='', array $in=[]) {
		if ('' !== $where && '' !== $in) {
			$this->query=$this->query." WHERE ".$where." IN (".implode(',', $in).")";
		}

		return $this;
	}

	/**
	* @param 	$where <String>
	* @param 	$contains <String>
	* @access 	public
	* @return 	Object
	*/	
	public function whereContains($where='', $contains='') {
		if ('' !== $where && '' !== $contains) {
			$this->query = $this->query." WHERE ".$where." LIKE '".$contains."%'";
		}

		return $this;
	}

	/**
	* @param 	$where <String>
	* @param 	$pattern <String>
	* @access 	public
	*/	
	public function wherehasPattern($where='', $pattern='') {
		if ('' !== $where && '' !== $pattern) {
			$this->query = $this->query." WHERE ".$where." LIKE '%".$startsWith."%'";
		}

		return $this;
	}

	/**
	* @param 	$where <String>
	* @param 	$startsWith <String>
	* @access 	public
	* @return 	Object
	*/	
	public function whereStartsWith($where='', $startsWith='') {
		if ('' !== $where && '' !== $startsWith) {
			$this->query = $this->query." WHERE ".$where." LIKE '[".$startsWith."]%'";
		}

		return $this;
	}

	/**
	* Attaches an update statement to the query stringproperty.
	*
	* @param 	$table <String>
	* @param 	$condition <String>
	* @access 	public
	* @return 	Object
	*/
	public function update($table='', $condition='') {
		if ('' !== $table && '' !== $condition) {
			$this->query = "UPDATE ".$table." SET ".$condition;
		}
		return $this;
	}

	/**
	* Retrieves the last insert id of the last insert operation.
	*
	* @access 	public
	* @return 	Integer
	*/
	public function insertId() {
		return $this->adapter->insertId($this->connection);
	}

	/**
	* @param 	$query <String>
	* @access 	public
	*/
	public function query($query='') {
		if (empty($query)) {
			throw new DatabaseException('Cannot execute empty query.');
		}

		try {
			$this->query = $query;
			$this->queryResult = $this->connection->query($this->query);
			if (!$this->queryResult) {
				return [];
			}
		}catch(\Exception $exception) {
			throw new DatabaseException($exception->getMessage());
		}

		return $this;
	}

	/**
	* Returns the number of rows.
	*
	* @access 	public
	* @return 	Integer
	*/
	public function numRows() {
		return $this->adapter->adapter()->numRows($this->queryResult);
	}

	/**
	* Checks if a table exists.
	*
	* @param 	$table <String>
	* @access 	public
	* @return 	Object
	*/
	public function showTable($table='') {
		$this->query = "SHOW TABLES LIKE '$table'";
		$this->execute();
		return $this;
	}

	/**
	* @param 	$table  <String>
	* @param 	$column <String>
	* @access 	public
	* @return 	Object
	*/
	public function showColumn($table='', $column='') {
		$this->query = "SHOW COLUMNS FROM $table LIKE '$column%'";
		$this->execute();
		return $this;
	}

	/**
	* Returns the escape stringfunction used by the adapter.
	*
	* @param 	$string <String>
	* @access 	public
	*/
	public function escape($string) {
		return $this->adapter->escapeString($this->connection, $string);
	}

	/**
	* Returns stat information about the database connection.
	*
	* @access 	public
	* @return 	String
	*/
	public function stat() {
		return $this->adapter->adapter()->stat($this->connection);
	}

	/**
	* Executes a prepared query.
	*
	* @param 	$query <String>
	* @access 	public
	*/
	public function execute($query='') {
		if ('' !== $query) {
			$this->query = $query;
		}
		
		$this->queryResult = $this->connection->prepare($this->query);
		$this->queryResult->execute();
		return $this->queryResult;
	}

	/**
	* Fetches a dataset result as an object.
	*
	* @param 	$param String
	* @access 	public
	*/
	public function get($param='') {
		if ('' == $param) {
			$param = __CLASS__;
		}

		// If the query result is empty, an attempt is made to run the query in here....
		if (null == $this->queryResult) {
			$this->query($this->query);
		}

		$this->factoryResult = $this->adapter->getObject($this->queryResult, $param);
		return $this;
	}

	/**
	* Returns the first record of a query result.
	* @access 	public
	* @return 	Object
	*/
	public function first() {
		if (null !== $this->factoryResult) {
			if (isset($this->factoryResult[0])) {
				return $this->factoryResult[0];
			}
		}
		return new StdClass;
	}

	/**
	* @param 	$offset <Integer>
	* @access 	public
	* @return 	Object
	*/
	public function offset($offset = 0) {
		if (!is_int($offset) || null === $this->factoryResult) {
			return new StdClass;
		}

		if (isset($this->factoryResult[$offset])) {
			return $this->factoryResult[$offset-1];
		}
	}

	/**
	* Returns query results fetched from database.
	*
	* @access 	public
	* @return 	Object
	*/
	public function all() {
		return $this->factoryResult;
	}

	/**
	* Returns the last record of a query result.
	*
	* @access 	public
	* @return 	Object
	*/
	public function last() {
		if (null !== $this->factoryResult) {
			$resultCount = sizeof($this->factoryResult);
			$resultCount = $resultCount - 1;
			return $this->factoryResult[$resultCount];
		}

		return new StdClass;
	}

	/**
	* Fetches a dataset result as an array.
	*
	* @param 	$param
	* @access 	public
	* @return 	Array
	*/
	public function getArray($param='') {
		// If the query result is empty, an attempt is made to run the query in here....
		if (null == $this->queryResult) {
			$this->query($this->query);
		}

		return $this->adapter->getArray($this->queryResult, $param);
	}

	/**
	* Returns protected query string.
	*
	* @access 	public
	* @return 	String
	*/
	public function getQuery() {
		return (isset($this->query)) ? $this->query : 'null';
	}

	/**
	* @access 	public
	*/
	public function resetQuery() {
		return ($this->query=null);
	}

	/**
	* Begins a transaction.
	*
	* @param 	$transactionParameter <String>
	* @access 	public
	* @return 	void
	*/
	public function beginTransaction($transactionParameter='') {
		return $this->adapter->adapter()->beginTransaction($this->connection, $transactionParameter);
	}

	/**
	* Commits a transaction.
	*
	* @access 	public
	* @return 	void
	*/
	public function commit() {
		return $this->adapter->adapter()->commit($this->connection);
	}

	/**
	* Roll backs a transaction.
	*
	* @access 	public
	*/
	public function rollBack() {
		return $this->adapter->adapter()->rollBack($this->connection, $this);
	}

	/**
	* @param 	$parameters <Array>
	* @access 	public
	* @return 	String
	*/
	protected function createPlaceholders(Array $parameters=[]) {
		return implode(',', array_fill(0, count($parameters), '?'));
	}

	/**
	* Validates an error info type.
	*
	* @param 	$error <String>
	* @access 	private
	* @return 	Boolean
	*/
	private function isErrorFunction($error='') {
		$response = false;
		if ('' !== $error) {
			if (preg_match('/.*[a-zA-Z]\(\)/', $error)) {
				$response = true;
			}
		}
		return $response;
	}

	/**
	* Sets up different error type handlers for different prepare types.
	*
	* @param 	$statement Object
	* @access 	public
	* @return 	void 
	*/
	protected function loadErrors($statement='') {
		if ('' !== $statement) {
			$connection = $statement;
		}else{
			$connection = $this->connection;
		}
	}

	/**
	* Returns error code of the error occured.
	*
	* @access 	public
	* @return 	Integer
	*/
	public function errorCode() {
		return $this->adapter->errorCode($this->connection);
	}

	/**
	* Returns information about error occured.
	*
	* @access 	public
	* @return 	String
	*/
	public function errorInfo() {
		$info = $this->adapter->errorInfo($this->connection);
		if (is_array($info)) {
			$info = $info[1].':'.$info[2];
		}
		return $info;
	}


	/**
	* Var dumps the database object.
	*
	* @access 	public
	* @return 	Object
	*/
	public function debug() {
		pre($this);
	}

}