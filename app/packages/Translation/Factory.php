<?php
namespace Package\Translation;

use RuntimeException;
use Package\Translation\Locale\Message;
use Package\Translation\Locale\LocaleManager;
use Package\DependencyInjection\Injector\InjectorBridge;
use Package\Translation\Exceptions\BadConfigurationSourceException;

class Factory extends InjectorBridge {

	/**
	* @var 		$locale
	* @access 	public
	*/
	public 		$locale;

	/**
	* @param 	$locale Package\Translation\Locale\LocaleManager
	* @access 	public
	* @return 	void
	*/
	public function __construct(LocaleManager $locale) {
		$this->locale = $locale;
	}

	/**
	* @param 	$message <String>
	* @param 	$parameters <Array>
	* @access 	public
	* @return 	String
	*/
	public function getMessage($message='', array $parameters=[]) {
		$messageLocale = new Message($message, $this->locale);
		return $messageLocale->getMessage($this, $parameters);
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function setLocale() {
		return $this->locale->setLocale();
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getLocale() {
		return $this->locale->getLocale();
	}

	/**
	* @param 	$key <String>
	* @access 	public
	* @return 	Array
	*/
	public function getConfig($key='') {
		$config = include 'public/config/translation.php';
		if (gettype($config) !== 'array') {
			throw new BadConfigurationSourceException("Invalid configuration file");
		}

		return $config[$key] ?? $config;
	}

}