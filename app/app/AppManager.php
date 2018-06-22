<?php
/**
* @author 		Peter Taiwo <peter@phoxphp.com>
* @package 		App\AppManager
* @license 		MIT License
*
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

namespace App;

use App\Config;
use View\Manager;
use RuntimeException;
use Kit\View\ArgResolver;
use App\ExceptionHandler;
use Kit\DependencyInjection\Factory as Container;

class AppManager extends Container
{

	/**
	* @var 		$services
	* @access 	protected
	*/
	protected static $services;

	/**
	* @var 		$init
	* @access 	private
	*/
	private static $init = false;

	/**
	* @var 		$responseCode
	* @access 	protected
	*/
	protected 	$responseCode = 500;

	/**
	* @access 	public
	* @return 	<void>
	*/
	public function __construct()
	{
		$servicesConfig = AppManager::appLibExt(
			baseDir('public' . DS . 'config' . DS . 'services')
		);

		$di = $this;

		// loading configured services...
		include $servicesConfig;
		
		if (php_sapi_name() !== 'cli') {
			register_shutdown_function([$this, 'fatalShutdown']);
			set_error_handler([$this, 'shutdown']);
			set_exception_handler([new ExceptionHandler(), 'handleException']);
		}
	}

	/**
	* Returns a new self of the app object. This method is useful when
	* trying to call a bound service.
	* Note: Creating a new instance of app object might break the application state.
	*
	* @access 	public
	* @return 	<Object> <App\AppManager>
	*/
	public static function getInstance()
	{
		return new self;
	}

	/**
	* @param 	$file <String>
	* @access 	public
	* @return 	<String>
	*/
	public static function appLibExt($file='')
	{
		return $file . '.php';
	}

	/**
	* @param 	$errorNumber <Integer>
	* @param 	$errorString <String>
	* @param 	$errorFile 	 <String>
	* @param 	$errorLine 	 <Integer>
	* @param 	$context 	<Object>
	* @access 	public
	* @return 	<void>
	*/
	public function shutdown($errorNumber, $errorString, $errorFile ='', $errorLine = 0, $isException=false, $context=null)
	{
		$errors = [
			'number'  => $errorNumber,
			'message' => $errorString,
			'file'	  => $errorFile,
			'line'	  => $errorLine,
		];

		if (!empty($errors)) {
			$site_url = config('app')->get('app_url');
			$devMode = config('app')->get('devMode');
			$prodErrorMessage  = config('app')->get('production_error_message');			

			$logger = getLogger('FileLogger', [
				'extension' => 'log',
				'file' => baseDir(config('app')->get('log_path') . 'app')
			]);

			$responseCode = 500;

			if ($isException == true) {
				$responseCode = $errorNumber;
			}

			ob_start();
			debug_print_backtrace();

			$trace = ob_get_clean();
			if ($isException == true && $context !== null) {
				$trace = $context->getTraceAsString();
			}

			$logger->log($errorString . "\n" . 'Debug Trace: ' . "\n" . $trace);
			include htmlFile(appDir('templates/errors/default'));
		}
	}

	/**
	* @access 	public
	* @return 	<void>
	*/
	public function fatalShutdown()
	{
		$error = @error_get_last();
		
		if($error) {
			$this->shutdown(
				$error['type'],
				$error['message'],
				$error['file'],
				$error['line'],
				false,
				$error
			);
		}
	}

	/**
	* @param 	$boot <Boolean>
	* @access 	public
	* @return 	<void>
	*/
	public function boot($boot=false)
	{
		if (true === boolval($boot)) {
			try {
				if (boolval(AppManager::$init) == true) {
					throw new Exception("Application is already running.");
				}

				AppManager::$init = true;
			}catch(Exception $e) {
				exit($e->getMessage());
			}

			if (php_sapi_name() !== 'cli') {
				$this->startApplication();
			}
		}
	}

	/**
	* Runs application.
	*
	* @access 	private
	* @return 	<void>
	*/
	private function startApplication()
	{
		$route = new \Kit\Http\Router\Repository(new \Kit\Http\Request\RequestManager());
		$routesFile = baseDir(config('app')->get('app_routes'));

		include $routesFile;
		$route->run();
	}

	/**
	* This `configure` method can be used to change php' ini settings(not recommended)
	* and can also be used to set which configuration you want to be able to access using
	* `getenv` function.
	*
	* @param 	$configurable <Closure>
	* @param 	$autoloader <Mixed>
	* @access 	public
	* @return 	<void>
	*/
	public function configure($configurable, $autoloader=null)
	{
		if ($configurable instanceof Closure) {

			$configurable = call_user_func($configurable, $this->load('config'));
			
			if (gettype($configurable) !== 'array') {
				throw new RuntimeException('Unable to set application configurations');
			}

			$configurable = (Object) $configurable;
			
			if (isset($configurable->sys) && sizeof($configurable->sys) > 0) {
				
				array_map(function($k) use ($configurable) {
					if (!is_numeric($k)) {
						ini_set($k, $configurable->sys[$k]);
					}
				}, array_keys($configurable->sys));

			}

			if (isArray($configurable->env_push)) {
				
				array_map(function($config){
				
					if (config()->get($config) && isArray(config()->get($config))) {

						$configArray = config()->get($config);
						
						foreach(array_keys($configArray) as $configKey) {

							putenv($configKey . "=" . $configArray[$configKey]);
						
						}

					}
				
				}, $configurable->env_push);

			}
		}

		if ($autoloader !== null) {
			include $autoloader;
		}
	}

	/**
	* Checks if a controller class exists.
	*
	* @param 	$controllerName <String>
	* @access 	public
	* @return 	<Boolean>
	*/
	public function hasController(String $controllerName=null) : Bool
	{
		$namespace = config('app')->get('controller_namespace');
		if (class_exists($namespace . $controllerName)) {
			return true;
		}

		return false;
	}

	/**
	* Checks if a model class exists.
	*
	* @param 	$modelName <String>
	* @access 	public
	* @return 	<Boolean>
	*/
	public function hasModel(String $modelName=null) : Bool
	{
		$namespace = config('app')->get('model_namespace');
		if (class_exists($namespace . $modelName)) {
			return true;
		}

		return false;
	}

	/**
	* Returns string name of a controller class with it's namespace.
	*
	* @param 	$controllerName <String>
	* @access 	public
	* @return 	<String>
	*/
	public function getControllerClassName(String $controllerName) : String
	{
		return config('app')->get('controller_namespace') . $controllerName;
	}

	/**
	* Returns string name of a model class with it's namespace.
	*
	* @param 	$modelName <String>
	* @access 	public
	* @return 	<String>
	*/
	public function getModelClassName(String $modelName) : String
	{
		return config('app')->get('model_namespace') . $modelName;
	}

}