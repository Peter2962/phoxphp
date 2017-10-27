<?php
/**
* @author 	Peter Taiwo
* @version 	1.0.0
* ###############################################
* This file is part of phoxphp framework.
* ###############################################
*/
namespace Package\Http\Session\Drivers;

use Package\Http\Session\Drivers\Interfaces\DriverInterface;
use Package\Http\Session\Factory;
use StdClass;

trait NativeDriverCommand {

	/**
	* @param 	$command <String>
	* @access 	public
	* @return 	void
	*/
	public static function runCommand($command) {
		$command = "?> $command <?php ";
		return eval($command);
	}

}
 
class NativeDriver implements DriverInterface {

	use NativeDriverCommand;

	/**
	* @var 		$factory
	* @access 	protected
	*/
	protected	$factory;

	/**
	* @var 		$nullKeyTypes
	* @access 	protected
	*/
	protected 	$nullKeyTypes = ['object', 'array', 'closure', 'boolean'];

	/**
	* @var 		$defaults
	* @access 	protected
	*/
	protected 	$defaults = ['keyname' => 'app-session-store-key'];

	/**
	* @var 		$key
	* @access 	protected
	*/
	protected 	$key;

	/**
	* @var 		$offset
	* @access 	protected
	*/
	protected 	$offset;

	/**
	* @var 		$store
	* @access 	protected
	*/
	protected 	$store;

	/**
	* {Constructor}
	*
	* @param 	$factory Http\Session\Factory
	* @access 	public
	* @return 	void
	*/
	public function __construct(Factory $factory) {
		$this->factory = $factory;
		$storage = $this->config()->storage;
		session_save_path($storage);
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	}

	/**
	* @access 	public
	* @return 	Boolean
	*/
	public function register() {
		return true;
	}

	/**
	* @param 	$key <String>
	* @param 	$value <Mixed>
	* @param 	$duration <Integer>
	* @access 	public
	* @return 	void
	*/
	public function create($key=null, $value=null, $duration=60) {
		if ($this->exists($key)) {
			return;
		}

		$timeout = (is_integer($duration)) ? $duration : $this->getTimestamp($duration);
		if (in_array(gettype($key), $this->nullKeyTypes)) {
			$key = 'app-session-store-key';
		}

		$_SESSION[$key] = [
			$this->encrypt($key) => $value,
			't' => time().'|'.$timeout
		];
	}

	/**
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function exists($key=null) {
		$reader = $this->read($key);
		$cmd = '<?php return isset($_SESSION'.$reader.'); ?>';
		$cmd = NativeDriverCommand::runCommand($cmd);
		if ($cmd) {
			return true;
		}
		return false;
	}

	/**
	* @param 	$key <String>
	* @access 	public
	* @return 	void
	*/
	public function delete($key=null) {
		if ($this->exists($key)) {
			$cmd = '<?php unset($_SESSION'.$this->read($key).'); ?>';
			return NativeDriverCommand::runCommand($cmd);
		}
	}

	/**
	* @access 	public
	* @return 	Object
	*/
	public function get() : NativeDriver {
		$this->store = $this->store();
		return $this;
	}

	/**
	* @param 	$toObject <Boolean>
	* @access 	public
	* @return 	Array|Object
	*/
	public function all($toObject=false) {
		$response = $_SESSION;
		if (boolval($toObject) == true) {
			$response = (Object) $response;
		}
		return $response;
	}

	/**
	* @access 	public
	* @return 	Mixed
	*/
	public function first() {
		if (sizeof($_SESSION) < 1) {
			return;
		}
		foreach(array_keys($_SESSION) as $i => $key) {
			if ($i == 0) {
				return $_SESSION[$key][$this->encrypt($key)];
			}
		}
	}

	/**
	* @access 	public
	* @return 	Mixed
	*/
	public function last() {
		if (sizeof($_SESSION) < 1) {
			return;
		}
		$lastOffset = sizeof($_SESSION) - 1;
		foreach(array_keys($_SESSION) as $i => $key) {
			if ($i == $lastOffset) {
				return $_SESSION[$key][$this->encrypt($key)];
			}
		}
	}

	/**
	* @param 	$key <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function offset($key=null) {
		if ($this->exists($key)) {
			$reader = $this->read($key);
			$cmd = '<?php return $_SESSION'.$reader.'; ?>';
			$cmd = NativeDriverCommand::runCommand($cmd);
			return $cmd;
		}
		return false;
	}

	/**
	* @access 	public
	* @return 	Boolean
	*/
	public function deleteAll() {
		if (sizeof($_SESSION) > 0) {
			session_destroy();
			session_unset();
		}
		return true;
	}

	/**
	* Deletes all sessions except for the ones provided in the array
	* parameter.
	*
	* @param 	$whiteList <Array>
	* @access 	public
	* @return 	Boolean
	*/
	public function deleteAllExcept(array $whiteList=[]) {
		if (sizeof($_SESSION) > 0) {
			$sessionKeys = array_keys($_SESSION);
			array_map(function($key) use ($whiteList) {
				$encryptedKey = $this->encrypt($key);
				if (!in_array($key, $whiteList) && isset($_SESSION[$key])) {
					unset($_SESSION[$key]);
				}
			}, array_keys($_SESSION));
		}
		return true;
	}

	/**
	* @access 	public
	* @return 	Array|Object
	*/
	public function config() {
		return $this->factory->getConfiguration();
	}

	/**
	* @param 	$key <String>
	* @access 	public
	* @return 	Object
	*/
	public function getCreatedDate($key=null) {
		if (sizeof(explode('|', $key)) > 1 || !$this->exists($key) || !$this->exists($key.'|t')) {
			return false;
		}
		$key = $key.'|t';
		$key = explode('|', $this->get()->offset($key));
		return $key[0];
	}

	/**
	* @param 	$key <String>
	* @access 	public
	* @return 	Integer
	*/
	public function getTimeout($key=null) {
		if (sizeof(explode('|', $key)) > 1 || !$this->exists($key) || !$this->exists($key.'|t')) {
			return false;
		}
		$key = $key.'|t';
		$key = explode('|', $this->get()->offset($key));
		return $key[1];
	}

	/**
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function isExpired($key=null) {
		$response = false;
		if (!$this->exists($key)) {
			return true;
		}

		$key = $this->getSessionTime($key);
		if (time() > bcadd($key[0], $key[1])) {
			$response = true;
		}
		
		return $response;
	}

	/**
	* @param 	$Key <String>
	* @param 	$timeout <Integer>
	* @access 	public
	* @return 	void
	*/
	public function incrementTimeout($key=null, $timeout=60) {
		if (!$this->exists($key)) {
			return;
		}
		$time = $this->getSessionTime($key);
		$duration = (is_int($timeout)) ? $timeout : $this->getTimestamp($timeout);
		$duration = bcadd($time[1], $duration);

		$_SESSION[$key]['t'] = $time[0].'|'.$duration;
	}

	/**
	* @param 	$key <String>
	* @param 	$timeout <Integer>
	* @access 	public
	* @return 	void
	*/
	public function decrementTimeout($key=null, $timeout=60) {
		if (!$this->exists($key)) {
			return;
		}
		$time = $this->getSessionTime($key);
		$duration = (is_int($timeout)) ? $timeout : $this->getTimestamp($timeout);
		if ($duration > $time[1]) {
			return;
		}

		$duration = bcsub($time[1], $duration);
		$_SESSION[$key]['t'] = $time[0].'|'.$duration;
	}

	/**
	* Returns a session's time value.
	*
	* @param 	$key <String>
	* @access 	protected
	* @return 	Array
	*/
	protected function getSessionTime($key='') {
		if (sizeof(explode('|', $key)) > 1 || !$this->exists($key) || !$this->exists($key.'|t')) {
			return false;
		}
		$key = $key.'|t';
		return explode('|', $this->get()->offset($key));
	}

	/**
	* Returns object of php's global session variable.
	*
	* @access 	protected
	* @return 	Object
	*/
	protected function store() {
		return (Object) $_SESSION;
	}

	/**
	* Formats and returns a session string.
	*
	* @param 	$string <String>
	* @access 	protected
	* @return 	String
	*/
	protected function read($string='') {
		$string = explode('|', $string);
		$queue = [];
		if (sizeof($string) == 1) {
			$string[1] = $this->encrypt($string[0]);
		}
		// Converts exploded strings to array formatted string.
		foreach($string as $str) {
			$queue[] = '["'.$str.'"]';
		}
		return implode('', $queue);
	}

	/**
	* @param 	$timeout <Integer>
	* @access 	protected
	* @return 	Integer
	*/
	protected function getTimestamp($timeout) {
		$factoryTimeout = $this->config()->timeout;
		if (!is_int($factoryTimeout) || intval($factoryTimeout) < 1) {
			$factoryTimeout = $timeout;
		}
		return $factoryTimeout;
	}

	/**
	* Encrypts a session key using md5 and sha1 functions.
	*
	* @param 	$key <String>
	* @access 	protected
	* @return 	String
	*/
	protected function encrypt($key='') {
		return md5(sha1($key));
	}

}