<?php
#########################################
# This file is part of phoxphp framework.
#########################################
namespace Package\View\Engines\Lite\Mixins;

use StdClass;
use RuntimeException;
use Package\View\Engines\Lite\Factory;
use Package\View\Engines\Lite\Compiler;
use Package\View\Engines\Lite\System\Module\Bag;
use Package\View\Engines\Lite\System\Module\Parser;
use Package\View\Engines\Lite\Mixins\Interfaces\MixinInterface;

class FunctionMixin implements MixinInterface {

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
	* @var 		$modules
	* @access 	private
	*/
	private 	$modules;

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
		$modulesBag = new Bag();
		$this->modules = $modulesBag->getModules();
	}

	/**
	* @access 	public
	* @return 	Boolean
	*/
	public function register() {
		return true;
	}

	/**
	* Checks if the template has `if` directive.
	* @access 	protected
	* @return 	Boolean
	*/
	protected function hasFunction() {
		$template = ($this->skipFileToString == true) ? $this->template : $this->factory->getTemplateContent($this->template);
		$preg = preg_match_all("/\{\% (.*?)\((.*?)\) \%\}/", $template, $matches);
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

		if ($this->hasFunction()) {
			foreach($this->directives[0] as $iterator => $label) {
				$moduleName = $this->directives[1][$iterator];
				$moduleArguments = htmlentities($this->directives[2][$iterator]);
				$relatedModule = (isset($this->modules[$moduleName])) ? $this->modules[$moduleName] : false;

				if(!$relatedModule) {
					throw new RuntimeException(sprintf("%s module has not been imported.", $moduleName));
				}

				$moduleArguments = explode(",", $moduleArguments);
				if (sizeof($relatedModule['arguments']) !== sizeof($moduleArguments)) {
					throw new RuntimeException(sprintf("Invalid argument count in %s module.", $moduleName));
				}

				$moduleFunction = new StdClass;
				$moduleFunction->module = $moduleName;
				$moduleFunction->arguments = $moduleArguments;
				$parser = new Parser($this->factory, new Bag(), $moduleFunction);
				$parser->parse();
				$result = $parser->getParseResult();

				Compiler::addCustomOutput($label, $result);
			}
		}

		return $compiledArray;
	}
}