<?php
namespace Package\Database;
/**
* @author 		Peter Taiwo
* @version 		1.0.0
* @package 		Database.Schema
* @copyright 	MIT License
* @deprecated 	Since 1.0.0
*
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

use Package\Database\Schema\Interfaces\SchemaInterface;
use DependencyInjection\Injector\InjectorBridge;
use Package\Database\Exceptions\DatabaseException;
use Package\Database\Schema\Helper\Translatelist;
use Package\Database\Schema\Helper\Translator;
use Package\Database\Schema\Column;
use Package\Database\Schema\Table;
use Package\Database\Factory;
use Package\Database\Engine;

class Schema extends InjectorBridge implements SchemaInterface {

	/**
	* @var 		$schema
	* @access 	public
	*/
	public 		$schema=null;

	/**
	* @var 		$engine
	* @access 	public
	*/
	public 		$engine;

	/**
	* @var 		$__schemaCanOverride
	* @access 	private
	*/
	private 	$__schemaCanOverride=false;

	/**
	* CREATE_MODE is passed as parameter to create method when creating a schema that does not exist.
	*/
	const 		CREATE_MODE='SCHEMA_CREATE_MODE';

	/**
	* UPDATE_MODE is passed as a parameter to create method when attempting to create schema which 
	* already exists which will result into updating the schema.
	*/
	const 		UPDATE_MODE='SCHEMA_UPDATE_MODE';

	/**
	* Creates a new table.
	*
	* @param 	$tableName <String> Name of the table to create in database.
	* @param 	$columns\null <Closure>
	* @param 	$engine <String> Sets the engine of the schema to be created.
	* A valid engine is required or else an error will be returned.
	* @access 	public
	* @return 	void
	*/
	public function createTable($tableName='', callable $columns=null, $engine='InnoDB') {
		// Note: Setting a new name will override the name give in the constructor.
		$printer = $columns(new Column);
		$preparedColumns = $this->getPreparedColumns();
		$this->engine = $engine;

		if ($this->hasTable($tableName)) {
			return $this->__updateOrFailOnExist($tableName, $preparedColumns);
		}
		if (false == Engine::isValid($engine)) {
			throw new DatabaseException($this->load('en_msg')->getMessage('error_invalid_engine', ['engine' => $engine]));
		}

		$columns = mergeAll($preparedColumns);
		$columns = array_map([$this, 'removeInvalidKeyword'], $columns);
		$table = new Table($tableName, $columns, $this->engine);
		$table->create();
	}

	/**
	* Drops a table from the database. This method inherits drop method from Table object.
	*
	* @param 	$table <String>
	* @access 	public
	* @return 	void
	*/
	public function deleteTable($table='') {
		$tableInstance = $this->getTableInstance($table);
		$tableInstance->drop();
	}

	/**
	* Checks if a schema exists. The schema to check must be provided in the
	* constructor.
	*
	* @param 	$table <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function hasTable($table='') {
		return $this->getTableInstance($table)->exists();
	}

	/**
	* Renames a database table.
	*
	* @param 	$oldName <String>
	* @param 	$newName <String>
	* @access 	public
	* @return 	void
	*/
	public function renameTable($oldName='', $newName='') {
		return $this->getTableInstance($oldName)->rename($newName);
	}

	/**
	* Returns an instance of Database\Table\Table.
	*
	* @param 	$table <String>
	* @param 	$dataType <Closure>
	* @access 	public
	* @return 	Object
	*/
	public function table($table='', callable $dataType) {
		return $dataType(new Table($table));
	}

	/**
	* Changes the schema charater set and collation.
	*
	* @param 	$schema <String>
	* @param 	$character/'utf8' <String>
	* @param 	$collation/'utf8_general_ci' <String>
	* @access 	public
	* @return 	void
	*/
	public function charset($schema='', $character='utf8', $collation='utf8_general_ci') {
		if ('' == $schema) {
			throw new DatabaseException($this->load('en_msg')->getMessage('error_empty_schema_name_charset'));
		}

		$queryString = Translator::translate('sct', [$schema, $character, $collation]);
		return $this->getConnection()->query($queryString);
	}

	/**
	* Returns all tables in a schema/database.
	*
	* @param 	$table <String>
	* @access 	public
	* @return 	Array
	*/
	public function getAllTables() {
		$queryString = Translator::translate('st', []);
		return $this->getConnection()->query($queryString)->getArray();
	}

	/**
	* Returns an instance of \Database\Factory.
	*
	* @access 	public
	* @return 	Object
	*/
	public function getConnection() {
		return new Factory;
	}

	/**
	* This method tells the schema to override a schema if it exists or not.
	*
	* @param 	$response <Boolean>
	* @access 	public
	* @return 	Boolean
	*/
	public function allowOverride($response) {
		$this->__schemaCanOverride = $response;
		return $this->__schemaCanOverride;
	}

	/**
	* @param 	$column <Array>
	* @access 	public
	* @return 	Mixed
	*/
	public function removeInvalidKeyword($column=[]) {
		$response=false;
		if (null!==$column) {
			$response = preg_replace('/^FOREIGN_KEY_.*[0-9] /', '', $column);
		}
		return $response;
	}	

	/**
	* Returns an array of columns prepared to be added to the table.
	*
	* @access 	public
	* @return 	Array.
	*/
	public function getPreparedColumns() {
		return Column::$_columns;
	}

	/**
	* Checks if provided schema mode is allowed when creating a schema.
	*
	* @param 	$mode <String>
	* @access 	private
	* @return 	Boolean
	*/
	private function __allowSchemaMode($mode='') {
		$response = true;
		$allowedModes = [
			Schema::CREATE_MODE,
			Schema::UPDATE_MODE
		];

		if (false === in_array($mode, $allowedModes)) {
			$response = false;
		}
		return $response;
	}

	/**
	* Returns an instance of Table object.
	*
	* @access 	private
	* @return 	Object
	*/
	private function getTableInstance($tableName='') {
		return new Table($tableName);
	}

	/**
	* Updates a table if it exists in the database.
	*
	* @param 	$tableName <String>
	* @param 	$preparedColumns <Array>
	*/
	private function __updateOrFailOnExist($tableName, $preparedColumns) {}

}