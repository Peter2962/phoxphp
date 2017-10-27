<?php
########################################################
# This file is part of phoxphp framework template files.
########################################################
namespace Package\View\Engines\Lite;

use RuntimeException;
use Package\View\Engines\Lite\Factory;
use Package\View\Engines\Lite\System\Module\Module;

class Compiler {

	/**
	* @var 		$factory
	* @access 	protected
	*/
	protected 	$factory;

	/**
	* @var 		$template
	* @access 	protected
	*/
	protected 	$template;

	/**
	* @var 		$templateRealPath
	* @access 	protected
	*/
	protected 	$templateRealPath;

	/**
	* @var 		$transferEncoded
	* @access 	protected
	*/
	protected 	$transferEncoded = false;

	/**
	* @var 		$evaluateDefault
	* @access 	protected
	*/
	protected static $evaluateDefault = false;

	/**
	* @var 		$compiledOutput
	* @access 	protected
	*/
	protected static $compiledOutput = [];

	/**
	* @var 		$engineStorage
	* @access 	protected
	*/
	protected static $engineStorage = [];

	/**
	* @var 		$skipFileToString
	* @access 	protected
	*/
	protected 	$skipFileToString;

	/**
	* @var 		$blocks
	* @access 	protected
	*/
	protected static $blocks;

	/**
	* @param 	$factory Lite\Factory 
	* @param 	$encode <Boolean>
	* @param 	$template <String>
	* @param 	$skipFileToString <Boolean>
	* @access 	public
	* @return 	void
	*/
	public function __construct(Factory $factory, $template='', $encode=false, $skipFileToString=false) {
		$this->factory = $factory;
		$this->template = $template;
		$this->transferEncoded = $encode;
		$this->skipFileToString = $skipFileToString;

		if ($skipFileToString == false) {
			$this->templateRealPath = $templatePath = $this->factory->getTemplateBuild($this->template);
			if (!file_exists($templatePath)) {
				throw new RuntimeException(sprintf('Unable to load template %s', $templatePath));
			}
		}
	}

	/**
	* @access 	public
	* @return 	Mixed
	*/
	public function render() {
		$directives = $this->resolveDirectives();
		if (boolval(Compiler::$evaluateDefault) == true) {
			return;
		}

		$variables = Factory::getAllVariables();
		foreach(array_keys($variables) as $varKey) {
			$$varKey = $variables[$varKey];
		}

		$directivesArray = [];

		$template = ($this->skipFileToString == true) ? $this->template : $this->factory->getTemplateContent($this->template);
		$compiled = str_replace(array_keys(Compiler::$compiledOutput), array_values(Compiler::$compiledOutput), $template);

		if (boolval($this->transferEncoded) == true) {
			return $compiled;
		}

		$command = html_entity_decode($compiled);
		eval("?> $command <?php ");

	}

	/**
	* @param 	$raw <String>
	* @param 	$compiled <String>
	* @access 	public
	* @return 	void
	*/
	public static function addCustomOutput($raw='', $compiled='') {
		Compiler::$compiledOutput[$raw] = $compiled;
	}

	/**
	* @param 	$raw <String>
	* @access 	public
	* @return 	Boolean
	*/
	public static function isQueued($raw='') {
		if (isset(Compiler::$compiledOutput[$raw])) {
			return true;
		}
		return false;
	}

	/**
	* @param 	$block <String>
	* @param 	$template <String>
	* @access 	public
	* @return 	void
	*/
	public static function appendBlock($block='', $template='') {
		Compiler::$blocks[$block] = ['template' => $template];
	}

	/**
	* @param 	$block <String>
	* @access 	public
	* @return 	String
	*/
	public static function getBlock($block='') {
		return (isset(Compiler::$blocks[$block])) ? Compiler::$blocks[$block] : null;
	}

	/**
	* Resolves all available directives.
	*
	* @access 	protected
	* @return 	void
	*/
	protected function resolveDirectives() {
		if (empty($this->factory->getLoadedDirectives())) {
			return Compiler::$evaluateDefault = true;
		}

		$directiveOutput = [];
		array_map(function($directive) use ($directiveOutput) {
			$namespace = 'Package\\View\\Engines\\Lite\\Mixins\\';
			$mixinClass = $namespace.ucfirst($directive).'Mixin';
			if (!class_exists($mixinClass)) {
				throw new RuntimeException(sprintf("Mixin object %s not found.", $mixinClass));
			}

			$mixinClass = new $mixinClass($this->factory, $this->template, $this->skipFileToString);
			if (boolval($mixinClass->register()) == false) {
				return;
			}

			if (!empty($mixinClass->getOutput())) {
				Compiler::$compiledOutput[] = $mixinClass->getOutput();
			}

		}, $this->factory->getLoadedDirectives());

		return Compiler::$compiledOutput;
	}

	/**
	* @param 	$base <String>
	* @access 	protected
	* @return 	void
	*/
	protected static function appendToEngineStorage($base='') {}

}