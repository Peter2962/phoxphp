<?php
namespace Package\Translation\Locale;

use RuntimeException;
use Package\Translation\Locale\Interfaces\MessageInterface;
use Package\Translation\Locale\Interfaces\LocaleInterface;

class LocaleManager implements LocaleInterface {

	/**
	* English language code
	*/
	const 		ENGLISH = 'en';

	/**
	* French language code
	*/
	const 		FRANCE 	= 'fr';

	/**
	* Spanish language code
	*/
	const 		SPAIN 	= 'es';

	/**
	* Chinese language code
	*/
	const 		CHINESE = 'zh';

	/**
	* German language code
	*/
	const 		GERMAN 	= 'de';

	/**
	* @var 		$localeDefault
	* @access 	private
	*/
	private 	$localeDefault;

	/**
	* @var 		$languageCode
	* @access 	private
	*/
	private static $languageCode;

	/**
	* @var 		$country
	* @access 	private
	*/
	private static $country;

	/**
	* Constructor
	*
	* @param 	$languageCode <String>
	* @param 	$country <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct($languageCode='', $country='') {
		if (!function_exists('setlocale')) {
			throw new RuntimeException("setlocale function does not exist");
		}
		LocaleManager::$languageCode = $languageCode;
		LocaleManager::$country = $country;
	}

	/**
	* @param 	$method <String>
	* @param 	$arguments <Array>
	* @access 	public
	* @return 	Mixed
	*/
	public function __call($method, $arguments) {
		$criteriaMatch = preg_match('/(get)(.*[a-zA-Z0-9])/', $method, $match);
		$localeconv = localeconv();
		if (!method_exists($this, $method) && $criteriaMatch) {
			$method = $match[2];
			if (!isset($localeconv[$method])) {
				return;
			}
			return $localeconv[$method];
		}
	}

	/**
	* {@inheritDoc}
	*
	* @access 	public
	* @return 	void
	*/
	public function setLocale() {
		setlocale(LC_ALL, $this->getLocale());
	}

	/**
	* {@inheritDoc}
	*
	* @access 	public
	* @return 	String
	*/
	public function getLocale() {
		$locale = LocaleManager::$languageCode;
		if (LocaleManager::$country !== '') {
			$locale = $locale.'_'.LocaleManager::$country;
		}
		return $locale;
	}

	/**
	* Sets the locale language code.
	*
	* @param 	$languageCode <String>
	* @access 	public
	* @return 	void
	*/
	public function setLanguageCode($languageCode='') {
		LocaleManager::$languageCode = $languageCode;
	}

	/**
	* Sets the locale country.
	*
	* @param 	$country <String>
	* @access 	public
	* @return 	void
	*/
	public function setCountry($country='') {
		LocaleManager::$country = $country;
	}

	/**
	* Returns the language code set by Translation\LocaleManager::setLanguageCode
	*
	* @access 	public
	* @return 	String
	*/
	public function getLanguageCode() {
		return LocaleManager::$languageCode;
	}

	/**
	* Returns the country set by Translation\LocaleManager::getCountry
	*
	* @access 	public
	* @return 	String
	*/
	public function getCountry() {
		return LocaleManager::$country;
	}
}