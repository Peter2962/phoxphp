<?php
/**
* @author 	Peter Taiwo
* @version 	1.0.0
* ###############################################
* This file is part of phoxphp framework.
* ###############################################
*/
namespace Package\Http;

class Cookie {

	/**
	* @var 		$name
	* @access 	private
	*/
	private 	$name;

	/**
	* @var 		$value
	* @access 	private
	*/
	private 	$value;

	/**
	* @var 		$xpire
	* @access 	private
	*/
	private 	$expire;

	/**
	* @var 		$path
	* @access 	private
	*/
	private 	$path;

	/**
	* @var 		$domain
	* @access 	private
	*/
	private 	$domain;

	/**
	* @var 		$secure
	* @access 	private
	*/
	private 	$secure;

	/**
	* @var 		$httpOnly
	* @access 	private
	*/
	private 	$httpOnly;

	/**
	* @param 	$cookie <String>
	* @param 	$value <Mixed>
	* @param 	$expire <Integer>
	* @param 	$path <String>
	* @param 	$domain <String>
	* @param 	$secure <Boolean>
	* @param 	$httpOnly <Boolean>
	* @access 	public
	* @return 	void
	*/
	public function __construct($name='', $value='', $expire=60, $path='/', $domain='', $secure=false, $httpOnly = false) {
		$this->name = $name;
		$this->value = $value;
		$this->expire = $this->getTime($expire); // Cookie expiration time in seconds
		$this->path = $path;
		$this->domain = $domain;
		$this->secure = $secure;
		$this->httpOnly = $httpOnly;
	}

	/**
	* Creates a cookie with the provided options
	*
	* @access 	public
	* @return 	void
	*/
	public function create() {
		return setcookie(
			$this->name,
			$this->value,
			$this->expire,
			$this->path,
			$this->domain,
			$this->secure,
			$this->httpOnly
		);
	}

	/**
	* Checks if a cookie is present or is created.
	*
	* @param 	$cookie <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function exists($cookie='') {
		$response = false;
		if (isset($_COOKIE[$cookie])) {
			$response = true;
		}
		return $response;
	}

	/**
	* Returns a cookie's value.
	*
	* @param 	$cookie <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function get($cookie='') {
		$response = false;
		if ($this->exists($cookie)) {
			$response = $_COOKIE[$cookie];
		}
		return $response;
	}

	/**
	* Deletes a cookie.
	*
	* @param 	$cookie <String>
	* @access 	public
	* @return 	void
	*/
	public function delete($cookie='') {
		setcookie(
			$cookie,
			'',
			time() - 3600,
			$this->path,
			$this->domain,
			$this->secure,
			$this->httpOnly
		);
		return true;
	}

	/**
	* Returns the cookie's value.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public function getValue() {
		return $this->value;
	}

	/**
	* Returns the cookie path.
	*
	* @access 	public
	* @return 	String
	*/
	public function getPath() {
		return $this->path;
	}

	/**
	* Returns the cookie domain.
	*
	* @access 	public
	* @return 	String
	*/
	public function getDomain() {
		return $this->domain;
	}

	/**
	* @param 	$value <Integer>
	* @access 	private
	* @return 	Integer
	*/
	private function getTime($time='') {
		if (!ctype_digit($time)) {
			return 60;
		}
		return $time;
	}

}