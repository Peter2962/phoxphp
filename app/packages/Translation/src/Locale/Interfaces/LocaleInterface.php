<?php
namespace Package\Translation\Locale\Interfaces;

interface LocaleInterface {

	/**
	* @param 	$method
	* @param 	$arguments
	* @access 	public
	* @return 	Mixed
	*/
	public function __call($method, $arguments);

	/**
	* Sets locale using setlocale function.
	*
	* @access 	public
	* @return 	void
	*/
	public function setLocale();

	/**
	* Gets current locale.
	*
	* @access 	public
	* @return 	String
	*/
	public function getLocale();

	/**
	* @param 	$languageCode <String>
	* @access 	public
	* @return 	String
	*/
	public function setLanguageCode($languageCode='');

	/**
	* @param 	$country <String>
	* @access 	public
	* @return 	String
	*/
	public function setCountry($country='');

	/**
	* @access 	public
	* @return 	String
	*/
	public function getLanguageCode();

	/**
	* @access 	public
	* @return 	String
	*/
	public function getCountry();

}