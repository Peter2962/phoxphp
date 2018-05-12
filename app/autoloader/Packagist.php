<?php
/**
* @author 	Peter Taiwo
* @package 	Packagist
* @version 	1.0.0
* 
* Copyright (c) 2017 MIT License
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

use Packagist\Exceptions\InvalidPackageFileException;
use Packagist\Exceptions\PackageNotFoundException;
use Packagist\Interfaces\PackagistInterface;

trait Messages
{

	/**
	* @var 		$undefinedTypeKey
	* @access 	private
	*/
	private 	$undefinedTypekey 	= "Warning [Required key `type` not defined in package list.]";

	/**
	* @var 		$invalidType
	* @access 	private
	*/
	private 	$invalidType 		= "Warning[`Type` value %s is not a valid type.]";
	
	/**
	* @var 		$packageNotFound
	* @access 	private
	*/
	private 	$packageNotFound 	= "Warning[Package %s was not found.]";

	/**
	* @var 		$fileLibCheckMessage
	* @access 	private
	*/
	private 	$fileLibCheckMessage = "Unable to load package/library %s. Verify that file exists";

	/**
	* @var 		$repoLibCheckMessage
	* @access 	private
	*/
	private 	$repoLibCheckMessage = "Could not locate library path %s. Verify that library path exists.";

	/**
	* @var 		$packageFileNotFound
	* @access 	private
	*/
	private 	$packageFileNotFound = "Unable to load %s. Verify that file exists and is readable.";

	/**
	* @var 		$packageObjectNotFound
	* @access 	private
	*/
	private 	$packageObjectNotFound = "Class %s not found.";

	/**
	* @param 	$messageKey String
	* @param 	$param String
	* @access 	public
	* @return 	void
	*/
	public function getMessage($messageKey='', $param = '') {
		return str_replace('%s', $param, $this->$messageKey);
	}

}

class Packagist implements PackagistInterface
{

	use Messages;

	/**
	* @var 		$packageFile
	* @access 	protected
	*/
	protected 	$packageFile = 'packagist.json';

	/**
	* @var 		$processLogFile
	* @access 	protected
	*/
	protected 	$processLogFile = 'Storage'.DIRECTORY_SEPARATOR.'process.log';

	/**
	* @var 		$packageKeyList
	* @access 	protected
	*/
	protected 	$packageKeyList = ['type', 'lib', 'use', 'strict', 'contain'];

	/**
	* @var 		$packageKeyValues
	* @access 	protected
	*/
	protected 	$packageKeyValues = ['type' => ['lib', 'repo']];

	/**
	* @var 		$packageStream
	* @access 	protected
	*/
	protected 	$packageStream;

	/**
	* @var 		$displayErrors
	* @access 	protected
	*/
	protected 	$displayErrors = 0;

	/**
	* @param 	$options <Array>
	* @access 	public
	* @throws 	InvalidPackageFileException
	* @return 	void
	*/
	public function __construct(array $options = [])
	{
		if (isset($options['display_errors']) && intval($options['display_errors']) < 2) {
			$this->displayErrors = $options['display_errors'];
		}

		if (isset($options['package_file'])) {
			$this->packageFile = $options['package_file'];
		}

		if (isset($options['log_file'])) {
			$this->processLogFile = $options['log_file'];
		}
		
		$extension = pathinfo($this->packageFile, PATHINFO_EXTENSION);
		try {
			if (!is_file($this->packageFile) || $extension !== 'json' || !is_readable($this->packageFile)) {
				throw new InvalidPackageFileException($this->getMessage("packageFileNotFound", $this->packageFile));
			}
		}catch(InvalidPackageFileException $e) {
			exit($e->getMessage());
		}
	}

	/**
	* @param 	$verifySSL <Boolean>
	* @access 	public
	* @return 	void
	*/
	public function beginAutoload($verifySSL=true)
	{
		$context = [];
		if (false === boolval($verifySSL)) {
			$context = [
				'ssl' => [
					'verify_peer' => false,
					'verify_peer_name' => false
				]
			];
		}

		$streamContext = stream_context_create($context);
		$autoloader = json_decode(file_get_contents($this->packageFile, false, $streamContext));
		if (gettype($autoloader) !== 'array') {
			return;
		}
		
		$autoloader = array_values($autoloader);

		foreach($autoloader as $i => $package) {
			$stream = (Object) $package;
			if (!isset($stream->type)) {
				$this->logProcess($this->getMessage('undefinedTypekey'));
				return false;
			}

			if (!in_array($stream->type, $this->packageKeyValues['type'])) {
				$this->logProcess($this->getMessage('invalidType', $stream->type));
				return false;
			}

			if ('lib' === $stream->type) {
				$this->autoloadLibrary($stream);
			}else{
				$this->autoloadRepository($stream);
			}
		}
	}

	/**
	* Load a directory into the app environment.
	*
	* @param 	$package <Object>
	* @access 	private
	* @return 	void
	*/
	private function autoloadRepository(StdClass $package)
	{
		(Array) $packageClasses = [];
		(Array) $packageObjects = [];
		(Array) $containables = [];

		$type = $package->type;
		$path = baseDir($package->lib);

		$this->checkLibrary($path);

		$containables = (isset($package->contain)) ? explode(':', $package->contain) : [];

		clearstatcache();
		$path = $path . DIRECTORY_SEPARATOR;
		$pathGLob = glob("$path*");

		$containableInfo = array_map([$this, 'pathInfo'], $containables);

		(Array) $containQueue = [];

		foreach($pathGLob as $file) {
			// If path is not a file, skip process....
			if (!is_file($file)) {
				continue;
			}

			$packageClasses[] = $file;
			$libraryName = $this->pathInfo($file)->filename;
			$isSkippedFile = (isset($package->except) && is_array($package->except) && in_array($libraryName, $package->except));

			if ($isSkippedFile) {
				continue;
			}

			$libraryDir = $this->pathInfo($file)->dirname;
			$libraryExtension = $this->pathInfo($file)->extension;
			$libraryPath = $libraryDir . DIRECTORY_SEPARATOR . $libraryName . '.' . $libraryExtension;
			if (isset($package->contain) && sizeof($containables) > 0) {
				if (in_array($libraryName, $containables)) {
					$packageObjects[] = $libraryPath;
				}
			}else{
				$packageObjects = $packageClasses;
			}
		}

		return array_map([$this, '__include'], $packageObjects);
	}

	/**
	* @param 	$package <Object>
	* @access 	private
	* @return 	void
	*/
	private function autoloadLibrary(StdClass $package)
	{
		$type = $package->type;
		$library = $this->lib($package->lib);
		$this->checkLibrary(baseDir($library));

		clearstatcache();
		include baseDir($library);

		if (isset($package->strict) && intval($package->strict) == 1) {
			$this->checkObject($library);
		}
	}

	/**
	* @param 	$processMessage <String>
	* @access 	private
	* @return 	Boolean
	*/
	private function logProcess($processMessage='')
	{
		$logFile = $this->processLogFile;
		if (false == is_file($logFile)) {
			return false;
		}

		chmod($logFile, 0777);

		$dateString = strtotime(date('Y-m-d H:i:s'));
		$formattedDate = date('Y F i H:i:s', $dateString);
		$processMessage = sprintf('Process logged on : %s. Message : %s', $formattedDate, $processMessage);

		$writerHandle = fopen($logFile, 'a');
		fwrite($writerHandle, $processMessage."\n");

		fclose($writerHandle);
	}

	/**
	* @param 	$library <String>
	* @access 	private
	* @return 	String
	*/
	private function lib($library='')
	{
		return $library.'.php';
	}

	/**
	* @param 	$library <String>
	* @param 	$libType <String>
	* @access 	private
	* @return 	Boolean
	*/
	private function checkLibrary($library='', $libType='file')
	{
		(String) $message='';
		(Boolean) $checkCase = null;

		if ($libType == 'file') {
			$message = $this->getMessage('fileLibCheckMessage', $library);
			$checkCase = (file_exists($library) && is_readable($library));
		}else{
			$message = $this->getMessage('repoLibCheckMessage', $library);
			$checkCase = (file_exists($library) && is_dir($library) && is_readable($library));
		}

		if (!$checkCase) {
			$this->logProcess($this->getMessage('packageNotFound', $library));
			if ($this->displayErrors == 1) {
				throw new PackageNotFoundException($message);
			}
			return false;
		}
	}

	/**
	* @param 	$package <String>
	* @access 	private
	* @return 	Boolean
	*/
	private function checkObject($package='')
	{
		$packageName = $this->pathInfo($package)->filename;
		if ($this->pathInfo($package)->extension == 'php') {
			if (!class_exists($packageName)) {
				$this->logProcess($this->getMessage('packageObjectNotFound', $packageName));
				if ($this->displayErrors == 1) {
					throw new PackageNotFoundException($this->getMessage('packageObjectNotFound', $packageName));
				}
				return false;
			}
		}
	}

	/**
	* @param 	$path <String>
	* @access 	private
	* @return 	Object
	*/
	private function pathInfo($path)
	{
		return (Object) pathinfo($path);
	}

	/**
	* @param 	$object <Object>
	* @access 	private
	* @return 	Array
	*/
	private function __toArray(StdClass $object)
	{
		return (Array) $object;
	}

	/**
	* @param 	$file <String> 	
	* @access 	private
	* @throws 	PackageNotFoundException
	* @return 	void
	*/
	private function __include($file='')
	{
		if (!file_exists($file)) {
			$this->logProcess($this->getMessage('packageNotFound', $file));
			if ($this->displayErrors == 1) {
				throw new PackageNotFoundException($this->getMessage('packageNotFound', $file));
			}
			return false;
		}

		include $file;
	}

}