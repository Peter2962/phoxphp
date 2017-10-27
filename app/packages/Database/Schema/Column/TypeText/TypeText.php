<?php
namespace Database\Schema\Column;
/**
* @author 		Peter Taiwo
* @version 		1.0.0
* @package 		Database.Schema.Column.TypeText
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

use DependencyInjection\Injector\InjectorBridge;
use ArrayHelper;

class TypeText extends InjectorBridge {

	/**
	* @var 		$referenceOptions
	* @access 	private
	*/
	private static $referenceOptions=[
		'CASCADE',
		'SET DEFAULT',
		'SET NULL',
		'RESTRICT',
		'NO ACTION'
	];

	/**
	* @param 	$length <Integer>
	* @access 	public
	* @return 	String
	*/
	public static function int($length='') {
		return (String)"INT($length)";
	}

	/**
	* @param 	$length <Integer>
	* @access 	public
	* @return 	String
	*/
	public static function char($length='') {
		return (String)"CHAR($length)";
	}

	/**
	* @param 	$length <Integer>
	* @access 	public
	* @return 	String
	*/
	public static function varchar($length='') {
		return (String)"VARCHAR($length)";
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public static function tinytext() {
		return (String)"TINYTEXT";
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public static function text() {
		return (String)"TEXT";
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public static function longtext() {
		return (String)"LONGTEXT";
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public static function tinyblob() {
		return (String)"TINYBLOB";
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public static function blob() {
		return (String)"BLOB";
	}	

	/**
	* @access 	public
	* @return 	String
	*/
	public static function mediumblob() {
		return (String)"MEDIUMBLOB";
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public static function longblob() {
		return (String)"LONGBLOB";
	}	

	/**
	* @param 	$length <Integer>
	* @access 	public
	* @return 	String
	*/
	public static function binary($length) {
		return (String)"BINARY($length)";
	}

	/**
	* @param 	$length <Integer>
	* @access 	public
	* @return 	String
	*/
	public static function varbinary($length) {
		return (String)"VARBINARY($length)";
	}

	/**
	* @param 	$data <Array>
	* @access 	public
	* @return 	String
	*/
	public static function enum($data) {
		$enum = ArrayHelper::enquote((Array)$data);
		$enum = implode(', ', $enum);
		return (String)"ENUM($enum)";
	}

	/**
	* @param 	$length <Integer>
	* @access 	public
	* @return 	String
	*/
	public static function smallint($length='') {
		return (String)"SMALLINT($length)";
	}

	/**
	* @param 	$length <Integer>
	* @access 	public
	* @return 	String
	*/
	public static function bit($length) {
		return (String)"BIT($length)";
	}

	/**
	* @param 	$length <Integer>
	* @access 	public
	* @return 	String
	*/
	public static function tinyint($length='') {
		return (String)"TINYINT($length)";
	}

	/**
	* @param 	$length <Integer>
	* @access 	public
	* @return 	String
	*/
	public static function mediumint($length='') {
		return (String)"MEDIUMINT($length)";
	}

	/**
	* @param 	$length <Integer>
	* @access 	public
	* @return 	String
	*/
	public static function bigint($length='') {
		return (String)"BIGINT($length)";
	}

	/**
	* @param 	$length <Integer>
	* @access 	public
	* @return 	String
	*/
	public static function decimal($length='') {
		return (String)"DECIMAL($length)";
	}

	/**
	* @param 	$length <Integer>
	* @access 	public
	* @return 	String
	*/
	public static function float($length='') {
		return (String)"FLOAT($length)";
	}

	/**
	* @param 	$length <Integer>
	* @access 	public
	* @return 	String
	*/
	public static function double($length='') {
		return (String)"DOUBLE($length)";
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public static function date() {
		return (String)"DATE";
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public static function datetime() {
		return (String)"DATETIME";
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public static function timestamp() {
		return (String)"TIMESTAMP";
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public static function time() {
		return (String)"TIME";
	}

	/**
	* @param 	$comment <String>
	* @access 	public
	* @return 	String
	*/
	public static function comment($comment) {
		return (String)"COMMENT $comment";
	}

	/**
	* @param 	$keys <Array>
	* @access 	public
	* @return 	String
	*/
	public static function foreignkey(array $keys=[]) {
		$keys=implode(',', $keys);
		return (String)"FOREIGN KEY ($keys)";
	}

	/**
	* @param 	$referencedtable <String>
	* @param 	$columns <Array>
	* @access 	public
	* @return 	String
	*/
	public static function reference($referencedTable='', $columns=[]) {
		$columns = implode(',', $columns);
		return (String)"REFERENCES $referencedTable($columns)";
	}

	/**
	* @param 	$referenceOption <String>
	* @access 	public
	* @return 	String
	*/
	public static function ondelete($referenceOption='') {
		if (false === in_array($referenceOption, self::$referenceOptions)) {
			$referenceOption = 'CASCADE';
		}

		$referenceOption = strtoupper($referenceOption);
		return (String)"ON DELETE $referenceOption";
	}

	/**
	* @param 	$referenceOption <String>
	* @access 	public
	* @return 	String
	*/
	public static function onupdate($referenceOption='') {
		if (false === in_array($referenceOption, self::$referenceOptions)) {
			$referenceOption = 'CASCADE';
		}
		
		$referenceOption = strtoupper($referenceOption);
		return (String)"ON UPDATE $referenceOption";
	}	

}