<?php
namespace Package\Database\Schema;
/**
* @author 		Peter Taiwo
* @version 		1.0.0
* @package 		Database.Schema.Column
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

use Exception;
use InvalidArgumentException;
use InvalidTypeArgumentException;
use Package\Database\Schema\Column\TypeText;
use Package\DependencyInjection\Injector\InjectorBridge;
use Package\Database\Schema\Column\Interfaces\ColumnInterface;

class Column extends InjectorBridge implements ColumnInterface {

	/**
	* @var 		$columns
	* @access 	public
	*/
	public 		$columns = [];

	/**
	* @var 		$_columns
	* @access 	public
	*/
	public static $_columns=[];

	/**
	* @var 		$keys
	* @access 	public
	*/
	public static $keys=[];

	/**
	* @var 		$field
	* @access 	public
	*/
	public 		$field=null;

	/**
	* @var 		$fieldName
	* @access 	public
	*/
	public static $fieldName;

	/**
	* @var 		$characterSet
	* @access 	private
	*/
	private static $characterSet=false;

	/**
	* @var 		$collatSet
	* @access 	private
	*/
	private static $collatSet=false;

	/**
	* @var 		$keysHandle
	* @access 	private
	*/
	private 	$keysHandle='FOREIGN_KEY';

	/**
	* @var 		$staticField
	* @access 	private
	*/
	private static $staticField;

	/**
	* Represents CHAR datatype.
	* @param 	$length <Integer>
	* @access 	public
	* @return 	void
	*/
	public function char($field='', $length=11) {
		return $this->appendColumn($field, $length, 'char');
	}

	/**
	* Represents VARCHAR datatype.
	* @param 	$field 	<String>
	* @param 	$length <Integer>
	* @access 	public
	* @return 	void
	*/
	public function varchar($field='', $length=25) {
		return $this->appendColumn($field, $length, 'varchar');
	}

	/**
	* Represents a TINYTEXT datatype.
	* @param 	$field  <String>
	* @access 	public
	* @return 	void
	*/
	public function tinytext($field) {
		return $this->appendColumn($field, null, 'text');
	}

	/**
	* Represents a TEXT datatype.
	* @param 	$field  <String>
	* @access 	public
	* @return 	void
	*/	
	public function text($field) {
		return $this->appendColumn($field, null, 'text');
	}	

	/**
	* Represents a SMALLTEXT datatype.
	* @param 	$field  <String>
	* @access 	public
	* @return 	void
	*/
	public function smalltext($field) {
		return $this->appendColumn($field, null, 'smalltext');
	}
	
	/**
	* Represents a MEDIUMTEXT datatype.
	* @param 	$field <String>
	* @access 	public
	* @return 	void
	*/
	public function mediumtext($field) {
		return $this->appendColumn($field, null, 'mediumtext');
	}

	/**
	* Represents a LONGTEXT datatype.
	* @param 	$field <String>
	* @access 	public
	* @return 	void
	*/
	public function longtext($field) {
		return $this->appendColumn($field, null, 'longtext');
	}

	/**
	* Represents a TINYBLOB datatype.
	* @param 	$field <String>
	* @access 	public
	* @return 	void
	*/
	public function tinyblob($field) {
		return $this->appendColumn($field, null, 'tinyblob');
	}

	/**
	* Represents a MEDIUMBLOB datatype.
	* @param 	$field <String>
	* @access 	public
	* @return 	void
	*/
	public function mediumblob($field) {
		return $this->appendColumn($field, null, 'mediumblob');
	}

	/**
	* Represents a BLOB datatype.
	* @param 	$field <String>
	* @access 	public
	* @return 	void
	*/
	public function blob($field) {
		return $this->appendColumn($field, null, 'blob');
	}

	/**
	* Represents a LONGBLOB datatype.
	* @param 	$field <String>
	* @access 	public
	* @return 	void	
	*/
	public function longblob($field) {
		return $this->appendColumn($field, null, 'longblob');
	}

	/**
	* Represents a ENUM datatype.
	* @param 	$field <String>
	* @param 	$data <String>
	* @access 	public
	* @throws 	InvalidTypeArgumentException
	* @return 	void
	*/
	public function enum($field, Array $data=[]) {
		if (false == is_array($data) || empty($data)) {
			throw new InvalidTypeArgumentException($this->calli18n()->get('enum_invalid_datatype')->locale);
		}
		return $this->appendColumn($field, $data, 'enum');
	}

	/**
	* Represents a BINARY datatype.
	* @param 	$field <String>
	* @param 	$length <Integer>
	* @access 	public
	* @return 	void
	*/
	public function binary($field, $length=11) {
		return $this->appendColumn($field, $length, 'binary');
	}

	/**
	* Represents a VARBINARY datatype.
	* @param 	$field <String>
	* @param 	$length <Integer>
	* @access 	public
	* @return 	void
	*/
	public function varbinary($field, $length=11) {
		return $this->appendColumn($field, $length, 'varbinary');
	}

	/**
	* Represents an BIT datatype.
	* @param 	$field <String>
	* @param 	$length <Integer>
	* @access 	public
	* @return 	void
	*/
	public function bit($field='', $length=4) {
		return $this->appendColumn($field, $length, 'bit');
	}

	/**
	* Represents an TINYINT datatype.
	* @param 	$field <String>
	* @param 	$length <Integer>
	* @access 	public
	* @return 	void
	*/
	public function tinyint($field='', $length=11) {
		return $this->appendColumn($field, $length, 'tinyint');
	}
	
	/**
	* Represents an INT datatype.
	* @param 	$field <String>
	* @param 	$length <Integer>
	* @access 	public
	* @return 	void
	*/
	public function int($field='', $length=11) {
		return $this->appendColumn($field, $length, 'int');
	}

	/**
	* Represents a SMALLINT datatype
	* @param 	$field <String>
	* @param 	$legnth <Integer>
	* @access 	public
	* @return 	void
	*/
	public function smallint($field, $length) {
		return $this->appendColumn($field, $length, 'smallint');
	}

	/**
	* Represents a MEDIUMINT datatype
	* @param 	$field <String>
	* @param 	$legnth <Integer>
	* @access 	public
	* @return 	void
	*/
	public function mediumint($field, $length) {
		return $this->appendColumn($field, $length, 'mediumint');
	}
	
	/**
	* Represents a BIGINT datatype
	* @param 	$field <String>
	* @param 	$legnth <Integer>
	* @access 	public
	* @return 	void
	*/
	public function bigint($field, $length) {
		return $this->appendColumn($field, $length, 'bigint');
	}

	/**
	* Represents a DECIMAL datatype
	* @param 	$field <String>
	* @param 	$legnth <Integer>
	* @access 	public
	* @return 	void
	*/
	public function decimal($field, $length='10,0') {
		return $this->appendColumn($field, $length, 'decimal');
	}

	/**
	* Represents a FLOAT datatype
	* @param 	$field <String>
	* @param 	$legnth <Integer>
	* @access 	public
	* @return 	void
	*/
	public function float($field, $length='10,0') {
		return $this->appendColumn($field, $length, 'float');
	}

	/**
	* Represents a DOUBLE datatype
	* @param 	$field <String>
	* @param 	$legnth <Integer>
	* @access 	public
	* @return 	void
	*/
	public function double($field, $length='10,0') {
		return $this->appendColumn($field, $length, 'double');
	}

	/**
	* Represents a DATE datatype.
	* @param 	$field <String>
	* @access 	public
	* @return 	void
	*/
	public function date($field) {
		return $this->appendColumn($field, null, 'date');
	}

	/**
	* Represents a DATETIME datatype.
	* @param 	$field <String>
	* @access 	public
	* @return 	void
	*/
	public function datetime($field) {
		return $this->appendColumn($field, null, 'datetime');
	}

	/**
	* Represents a TIMESTAMP datatype.
	* @param 	$field <String>
	* @access 	public
	* @return 	void
	*/
	public function timestamp($field) {
		return $this->appendColumn($field, null, 'timestamp');
	}

	/**
	* Represents a TIME datatype.
	* @param 	$field <String>
	* @access 	public
	* @return 	void
	*/
	public function time($field) {
		return $this->appendColumn($field, null, 'time');
	}
	
	/**
	* Appends an AUTO_INCREMENT attribute to the current column.
	* @access 	public
	* @return 	void
	*/
	public function autoincrement() {
		return $this->appendAttribute("AUTO_INCREMENT");
	}
		
	/**
	* Appends a null keyword to the current field constraint.
	* @param 	$null <Boolean>
	* @access 	public
	* @return 	void
	*/
	public function null($null=false) {
		$nullType = (boolval($null)===true) ? "NULL" : "NOT NULL";
		return $this->appendAttribute($nullType);
	}

	/**
	* Appends a default option to the current column.
	* @param 	$default <String>
	* @access 	public
	* @return 	void
	* @throws 	InvalidTypeArgumentException
	*/
	public function isdefault($default='') {
		$default = "DEFAULT $default";
		return $this->appendAttribute($default);
	}

	/**
	* Appends an unsigned attribute to the column.
	* @access 	public
	* @return 	void
	*/
	public function unsigned() {
		$unsigned = "UNSIGNED";
		return $this->appendAttribute($unsigned);
	}
	
	/**
	* Appends a primary key option to the schema
	* @param 	$field <String>
	* @access 	public
	* @return 	void
	*/
	public function primary() {
		$attribute = "PRIMARY KEY";
		return $this->appendAttribute($attribute);
	}

	/**
	* Appends a foreign key constraint to the schema to be built.
	* @param 	$keys <Array>
	* @access 	public
	* @throws 	\InvalidTypeArgumentException
	* @return 	void
	*/
	public function foreignkey(array $keys=[]) {
		if (empty($keys)) {
			throw new InvalidTypeArgumentException($this->calli18n()->get('schema_null_attribute')->locale);
		}
		$length = sizeof(self::$_columns);
		$handle = $this->keysHandle.'_'.($length+1);
		return $this->appendColumn($handle, $keys, 'foreignkey');
	}

	/**
	* Appends the referenced columns  to a foreign key constraint.
	* @param 	$referencedTable <String>
	* @param 	$columns <Array>
	* @access 	public
	* @return 	void
	* @throws 	\InvalidTypeArgumentException
	*/
	public function reference($referencedTable='', array $columns=[]) {
		if (empty($columns)) {
			throw new InvalidTypeArgumentException($this->calli18n()->get('schema_null_attribute')->locale);
		}

		$columns = TypeText::reference($referencedTable, $columns);
		return $this->appendAttribute($columns);
	}

	/**
	* Appends ON DELETE reference option to the referenced columns.
	* @param 	$referenceOption <String>
	* @access 	public
	* @return 	void
	*/
	public function ondelete($referenceOption='') {
		$referenceOption = TypeText::ondelete($referenceOption);
		return $this->appendAttribute($referenceOption);
	}

	/**
	* Appends ON UPDATE reference option to the referenced columns.
	* @param 	$referenceOption <String>
	* @access 	public
	* @return 	void
	*/
	public function onupdate($referenceOption='') {
		$referenceOption = TypeText::onupdate($referenceOption);
		return $this->appendAttribute($referenceOption);
	}

	/**
	* Sets the character set of the schema
	* @param 	$charset <String>
	* @access 	public
	* @throws 	\InvalidTypeArgumentException
	* @return 	void
	*/
	public function charset($charset='') {
		if ('' == $charset) {
			throw new InvalidTypeArgumentException($this->calli18n()->get('schema_null_attribute')->locale);
		}
		$charset = "CHARACTER SET $charset";
		return $this->appendAttribute($charset);
	}

	/**
	* Sets collation name for the schema
	* @param 	$collate <String>
	* @access 	public
	* @throws 	\InvalidTypeArgumentException
	* @return 	void
	*/
	public function collate($collate='') {
		if ('' == $collate) {
			throw new InvalidTypeArgumentException($this->calli18n()->get('schema_null_attribute')->locale);
		}
		$collate = "COLLATE $collate";
		return $this->appendAttribute($collate);
	}

	/**
	* Adds a comment to a schema column.
	* @param 	$column <String>
	* @access 	public
	* @return 	void
	*/
	public function comment($comment='') {
		if ('' == $comment) {
			$comment = 'NULL';
		}
		return $this->appendAttribute(TypeText::comment($comment));
	}

	/**
	* Appends a basic index key to the schema column.
	* @access 	public
	* @return 	void
	*/
	public function index() {
		return $this->appendAttribute("INDEX");
	}

	/**
	* Appends a unique index to the schema column.
	* @access 	public
	* @return 	void
	*/
	public function unique() {
		return $this->appendAttribute("UNIQUE");
	}

	/**
	* Appends a `created_at` column to the schema.
	* @access 	public
	* @return 	void
	*/
	public function createdAt() {
		return $this->date('created_at');
	}

	/**
	* Appends a 'updated_at' column to the schema
	* @access 	public
	* @return 	void
	*/
	public function updatedAt() {
		return $this->date('updated_at');
	}

	/**
	* Appends a 'deleted_at' column to the schema
	* @access 	public
	* @return 	void
	*/
	public function deletedAt() {
		return $this->date('deleted_at');
	}

	/**
	* Appends 'is_deleted' column to the schema.
	* @access 	public
	* @return 	void
	*/
	public function isDeleted() {
		return $this->tinyint('is_deleted');
	}

	/**
	* Adds a column to the column.
	* @param 	$field <String>
	* @param 	$length <Integer>
	* @param 	$text <Object>
	* @access 	public
	* @return 	void
	*/
	public function appendColumn($field, $length='', $text) {
		$this->columns[$field] = TypeText::$text($length);
		$this->field = $field;
		self::$_columns[$field] = $this->columns[$field];
		Column::$fieldName = $field;
		Column::$staticField = self::$_columns[$field];
		return $this;
	}

	/**
	* Appends an attribute e.g 'default', 'null' to a column.
	* @param 	$attribute <String>
	* @access 	public
	* @return 	void
	* @throws	\InvalidTypeArgumentException
	*/
	public function appendAttribute($attribute='') {
		if (null === $this->field || '' == $attribute) {
			throw new InvalidTypeArgumentException($this->calli18n()->get('schema_null_attribute')->locale);
		}
		
		Column::$staticField = $columns = self::$_columns[$this->field] .= " $attribute";
		$this->columns[$this->field] = $columns;
		return $this;
	}

	/**
	* Returns \Column::$staticField
	* @access 	public
	* @return 	String
	*/
	public static function getStaticField() {
		return Column::$fieldName.' '.Column::$staticField;
	}

}