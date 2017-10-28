<?php
namespace Package\Database\Schema\Helper;

use Package\Database\Schema\Helper\Translatelist;

class Translator {

	/**
	* @var 		$translates
	* @access 	public
	*/
	public 		$translates=[];

	/**
	* @var 		$translateCallback
	* @access 	private
	*/
	private static $translateCallback;

	/**
	* @var 		$queryString
	* @access 	public
	*/
	public static $queryString;

	/**
	* @param 	$translates <Array>
	* @access 	public
	* @return 	void
	*/
	public function __construct(array $translates=[]) {
		$this->translates = $translates;
	}

	/**
	* @param 	$translateCallback <String>
	* @param 	$translateParameters <Array>
	* @access 	public
	* @return 	String
	*/
	public static function translate($translateCallback=null, array $translateParameters=[]) {
		Translator::$translateCallback = $translateCallback;
		$callback = Translator::getTranslateCallback(Translator::$translateCallback);
		Translator::$queryString = str_replace($callback, $translateParameters, Translatelist::$translateCallback());
		return Translator::$queryString;
	}

	/**
	* @param 	$callback <String>
	* @access 	private
	* @return 	Array
	*/
	private static function getTranslateCallback($callback='') {
		$callback = Translatelist::$callback();
		$translateParammeters = [];
		$string = explode(" ", $callback);

		$stringCallback = preg_grep('/^\{\{.*[a-z]\}\}/', $string);
		return array_values($stringCallback);
	}

}