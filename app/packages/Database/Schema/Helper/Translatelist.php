<?php
namespace Package\Database\Schema\Helper;

abstract class Translatelist {

	/**
	* Returns SHOW TABLE statement.
	*
	* @access 	public
	* @return 	String
	*/
	public static function st() {
		$text = "SHOW TABLES";
		return (String) $text;
	}

	/**
	* Returns CREATE TABLE statement.
	*
	* @access 	public
	* @return 	String
	*/
	public static function ct() {
		$text = "CREATE TABLE IF NOT EXISTS {{name}} ( {{columns}} )ENGINE= {{engine}}";
		return (String) $text;
	}	

	/**
	* Returns DROP TABLE statement.
	*
	* @access 	public
	* @return 	String
	*/
	public static function dt() {
		$text = "DROP TABLE {{table}}";
		return (String) $text;
	}

	/**
	* Returns RENAME TABLE statement.
	*
	* @access 	public
	* @return 	String
	*/
	public static function rt() {
		$text = "RENAME TABLE {{oldname}} TO {{newname}}";
		return (String) $text;
	}

	/**
	* Returns ALTER DATABASE statement.
	*
	* @access 	public
	* @return 	String
	*/
	public static function sct() {
		$text = "ALTER DATABASE {{database}} CHARACTER SET {{character}} COLLATE {{collation}}";
		return (String) $text;
	}

	/**
	* Returns ALTER TABLE statement.
	*
	* @access 	public
	* @return 	String
	*/
	public static function ac() {
		$text = "ALTER TABLE {{table}} ADD {{column}}";
		return (String) $text;
	}

	/**
	* Returns CREATE INDEX statement.
	*
	* @access 	public
	* @return 	String
	*/
	public static function ai() {
		$text = "CREATE INDEX {{indexname}} ON {{table}} ( {{indexes}} )";
		return (String) $text;
	}

	/**
	* Returns SHOW INDEX statement.
	*
	* @access 	public
	* @return 	String
	*/
	public static function gi() {
		$text = "SHOW INDEX FROM {{table}}";
		return (String) $text;
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public static function hix() {
		$text = "SHOW INDEX FROM {{table}} WHERE Key_name = {{index}}";
		return (String) $text;
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public static function aui() {
		$text = "CREATE UNIQUE INDEX {{indexname}} ON {{table}} ( {{indexes}} )";
		return (String) $text; 
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public static function pki() {
		$text = "ALTER TABLE {{table}} ADD PRIMARY KEY ( {{column}} )";
		return (String) $text; 
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public static function fti() {
		$text = "CREATE FULLTEXT INDEX {{indexname}} ON {{table}} ( {{indexes}} )";
		return (String) $text; 
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public static function di() {
		$text = "ALTER TABLE {{table}} DROP INDEX {{index}}";
		return (String) $text; 
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public static function dc() {
		$text = "ALTER TABLE {{table}} DROP COLUMN {{column}}";
		return (String) $text; 
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public static function dfk() {
		$text = "ALTER TABLE {{table}} DROP FOREIGN KEY {{key}}";
		return (String) $text; 
	}		

}