<?php
#########################################
# This file is part of phoxphp framework.
#########################################
namespace Package\View\Engines\Lite\Mixins;

use Exception;
use RuntimeException;
use Package\View\Engines\Lite\Factory;
use Package\View\Engines\Lite\Compiler;
use Package\View\Engines\Lite\System\Module\Bag;
use Package\View\Engines\Lite\System\Module\Module;
use Package\View\Engines\Lite\Mixins\Interfaces\MixinInterface;
use Package\View\Engines\Lite\Exceptions\InvalidModuleStorageException;

class ModuleMixin implements MixinInterface {

	/**
	* @var 		$factory
	* @access 	private
	*/
	private 	$factory;

	/**
	* @var 		$template
	* @access 	private
	*/
	private 	$template;

	/**
	* @var 		$directives
	* @access 	private
	*/
	private 	$directives = array();

	/**
	* @var 		$skipFileToString
	* @access 	private
	*/
	private 	$skipFileToString;

	/**
	* @var 		$module
	* @access 	private
	*/
	private 	$Module;

	/**
	* @var 		$moduleBag
	* @access 	private
	*/
	private 	$moduleBag;

	/**
	* @param 	$factory Lite\Factory
	* @param 	$template <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct(Factory $factory, $template, $skipFileToString=false) {
		$this->factory = $factory;
		$this->template = $template;
		$this->skipFileToString = $skipFileToString;
		$this->module = new Module($factory);
		$this->moduleBag = new Bag();
	}

	/**
	* @access 	public
	* @return 	Boolean
	*/
	public function register() {
		return true;
	}

	/**
	* @access 	protected
	* @return 	Boolean
	*/
	protected function hasModule() {
		$template = ($this->skipFileToString == true) ? $this->template : $this->factory->getTemplateContent($this->template);
		$preg = preg_match_all("/@import\((.*?)\) from \((.*?)\)/", $template, $matches);
		if (!$preg) {
			return false;
		}

		$this->directives = $matches;
		return true;
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getOutput() {
		$compiledArray = array();

		if ($this->hasModule()) {
			$importLabels = $this->directives[0];
			foreach($importLabels as $iterator => $label) {
				$importModule = str_replace(['"', "'"], '', $this->directives[1][$iterator]);
				$importModulePath = str_replace(['"', "'"], '', $this->directives[2][$iterator]);
				
				if (!$this->module->storageExists($importModulePath)) {
					throw new Exception(sprintf("%s module storage not found.", $importModulePath));
				}

				$importModules = explode(',', str_replace(' ', '', $importModule));
				array_map(function($module) use ($importModulePath) {
					if (isset($this->moduleBag->getModules()[$module])) {
						$this->moduleBag->removeModule($module);
					}

					$module = $this->module->findModule($module, $this->module->getPath());
				}, $importModules);

				Compiler::addCustomOutput($label, "");
			}
		}

		return $compiledArray;
	}
	
}