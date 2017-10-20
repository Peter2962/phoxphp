<?php
#########################################
# This file is part of phoxphp framework.
#########################################
namespace Package\View\Engines\Lite\System\Module;

use Package\View\Engines\Lite\Exceptions\ModuleNotFoundException;
use Package\View\Engines\Lite\System\Module\Builder;
use Package\View\Engines\Lite\System\Module\Bag;
use Package\View\Engines\Lite\Factory;

class Module {

	/**
	* @var 		$factory
	* @access 	private
	*/
	private 	$factory;

	/**
	* @var 		$systemModulePath
	* @access 	private
	*/
	private 	$systemModulePath;

	/**
	* @var 		$builder
	* @access 	private
	*/
	private 	$builder;

	/**
	* @var 		$path
	* @access 	private
	*/
	private 	$path;

	/**
	* @var 		$name
	* @access 	private
	*/
	private 	$name;

	/**
	* @var 		$arguments
	* @access 	private
	*/
	private 	$arguments;

	/**
	* @var 		$body
	* @access 	private
	*/
	private 	$body;

	/**
	* @param 	$factory Lite\Factory
	* @access 	public
	* @return 	void
	*/
	public function __construct(Factory $factory) {
		$this->factory = $factory;
		$this->builder = new Builder($this);
	}

	/**
	* Builds modules from the given module file
	* @access 	public
	* @return 	void
	*/
	public function buildModules($module='') {
		$this->builder->buildModule($module);
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getPath() {
		return $this->path;
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getTempName() {
		return $this->name;
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getTempBody() {
		return $this->body;
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getTempArguments() {
		return $this->arguments;
	}

	/**
	* Checks if a module storage, file exists.
	*
	* @param 	$path <String>
	* @access 	public
	*/
	public function storageExists($path='') {
		$storagePath = $this->factory->config('modules_path');
		if (file_exists($storagePath.$path.'.module')) {
			$this->path = $storagePath.$path.'.module';
			return true;
		}
		return false;
	}

	/**
	* @param 	$modules <String>
	* @param 	$path <String>
	* @access 	public
	* @return 	void
	*/
	public function findModule($module='', $path='') {
		$moduleBag = new Bag();

		$key = "/@map $module: \((.*?)\)(.*?)@end$module/s";
		$moduleSearch = preg_match_all($key, file_get_contents($path), $matches);
		if (!$moduleSearch) {
			throw new ModuleNotFoundException(sprintf("%s module does not exist", $module));
		}

		$this->name = $module;
		$this->body = $matches[2][0];
		$this->arguments = explode(",", str_replace(' ', '', $matches[1][0]));

		$moduleBag->pushModuleToStore($this);
	}

}