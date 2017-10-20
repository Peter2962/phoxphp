<?php
/**
* @author 	Peter Taiwo
* @package 	Lite.Factory
* @version 	1.0.0
* ##################################################
* This file is part of phoxphp framework core files.
* ##################################################
*/
namespace Package\View\Engines\Lite;

use Package\View\Engines\Lite\Hooks\OnCompileStart;
use Package\View\Engines\Lite\Compiler;
use RuntimeException;
use App\Config;
use Exception;

class Factory extends Config {

	/**
	* @var 		$template
	* @access 	protected
	*/
	protected 	$template;

	/**
	* @var 		$layout
	* @access 	protected
	*/
	protected 	$layout;

	/**
	* @var 		$EXT_DIRECTIVE_ENABLED
	* @access 	public
	*/
	public static $EXT_DIRECTIVE_ENABLED = false;

	/**
	* @var 		$ENCODE_DATA
	* @access 	private
	*/
	private static $ENCODE_DATA = false;

	/**
	* @var 		$ROUTE
	* @access 	public
	*/
	public static $ROUTE = array();

	/**
	* @var 		$DIRECTIVES
	* @access 	private
	*/
	private static $DIRECTIVES = array();

	/**
	* @var 		$loadedTemplates
	* @access 	private
	*/
	private static $loadedTemplates = array();

	/**
	* @var 		$templateVariablesStore
	* @access 	private
	*/
	private static $templateVariablesStore = array();

	/**
	* @var 		$skipFileToString
	* @access 	private
	*/
	private static $skipFileToString = false;

	/**
	* @var 		$templateTree
	* @access 	private
	*/
	private static $templateTree = array();

	/**
	* @const 	ENCODE_DATA
	*/
	const 		ENCODE_DATA = 'ENCODE_DIRECTIVE_DATA';

	/**
	* @const 	EMPTY_STAPLER
	*/
	const 		EMPTY_STAPLER = 'DATA_EMPTY_STAPLER';

	/**
	* @const 	SELF_TEMPLATE
	*/
	const 		SELF_TEMPLATE = '{{SELF}}';

	/**
	* Name of template is passed as an argument to the constructor.
	*
	* @param 	$template <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct($template='') {
		$this->template = $template;
	}

	/**
	* @param 	$template <String>
	* @access 	public
	* @return 	self <Object>
	*/
	public static function instance($template='') {
		return new self($template);
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getRegisteredFunctions() {
		return $this->config('registered_functions');
	}

	/**
	* @param 	$option <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function config($option='') {
		$config = $this->get('view.lite');
		if (gettype($config) !== 'array') {
			throw new Exception(sprintf("Array expected from config. %s Returned.", gettype($config)));
		}

		$config = (Object) $config;
		return (isset($config->$option)) ? $config->$option : '';
	}

	/**
	* @access 	public
	* @return 	Boolean
	*/
	public function isCodeDisabled() {
		if (boolval($this->config('disable_php_syntax')) == true) {
			return true;
		}
		return false;
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getTemplate() {
		return $this->template;
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getTemplatePath() {
		return $this->config('template_path');
	}

	/**
	* Returns the absolute path to a template file.
	*
	* @param 	$template <String>
	* @access 	public
	* @return 	String
	*/
	public function getTemplateRealPath($template='') {
		$template = $this->getTemplatePath().'/'.$template.'.'.$this->getExtension();
		return $template;
	}

	/**
	* @param 	$template <String>
	* @access 	public
	* @return 	String
	*/
	public function getTemplateContent($template='') {		
		$path = $this->getTemplateRealPath($template);
		if (!file_exists($path)) {
			throw new RuntimeException(sprintf("Unable to get template content in %s.", $path));
		}
		return file_get_contents($path);
	}

	/**
	* @param 	$template <String>
	* @access 	public
	* @return 	String
	*/
	public function getTemplateBuild($template='') {
		return $this->getTemplatePath().'/'.$template.'.'.$this->getExtension();
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getExtension() {
		return $this->config('template_extension');
	}

	/**
	* Renders a given template. THis method accepts two arguments: $template and $encode.
	* $template, as the name implies is the name of the template to be rendered while $encode
	* argument, if set to true will tell the script to return the encoded string of the template instead
	* of rendering it.
	*
	* @param 	$template <String>
	* @param 	$encode <Boolean>
	* @param 	$skipFileToString <Boolean> This variable will tell the compiler to render the template
	* as a string instead of a file.
	* @access 	public
	* @return 	String
	*/
	public function render($template='', $encode=false, $skipFileToString=false) {
		$compilerHook = new OnCompileStart($this, $skipFileToString);
		$compilerHook->runHookOn($template);
		return $this->getCompiler($template, $encode, $skipFileToString)->render();
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getDirective() {
		return Factory::$DIRECTIVES;
	}

	/**
	* Returns an array of templates rendered..
	* @access 	public
	* @return 	Array
	*/
	public function getRenderedTemplates() {
		return Factory::$loadedTemplates;
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getLayout() {
		return $this->layout;
	}

	/**
	* Returns an array of directives registered in the configuration
	* file.
	*
	* @access 	public
	* @return 	Array
	*/
	public function getLoadedDirectives() {
		return $this->config('directives');
	}

	/**
	* Returns an array of registered directive filters in the configuration
	* file.
	*
	* @access 	public
	* @return 	Array
	*/
	public function getLoadedFilters() {
		return $this->config('directive_filters');
	}

	/**
	* Adds a variable to the @param $templateVariablesStore.
	*
	* @param 	$variable <String>
	* @param 	$value <Mixed>
	* @access 	public
	* @return 	void
	*/
	public function setVariable($variable='', $value='') {
		Factory::$templateVariablesStore[$variable] = $value;
	}

	/**
	* Returns a template variable.
	*
	* @param 	$variable <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function getVariable($variable='') {
		return (!isset(Factory::$templateVariablesStore[$variable])) ? null : Factory::$templateVariablesStore[$variable];
	}

	/**
	* Returns all registered variables for a specific template.
	*
	* @access 	public
	* @return 	Array
	*/
	public static function getAllVariables() {
		return Factory::$templateVariablesStore;
	}

	/**
	* This method queues a template's data for use by different directives.
	*
	* @param 	$template <String>
	* @param 	$key <String>
	* @param 	$value <Mixed>
	* @access 	public
	* @return 	void
	*/
	public function pushToTemplateTree($template='', $key='', $value='') {
		if (isset(Factory::$templateTree[$template][$key])) {
			Factory::$templateTree[$template][$key][] = $value;
			return true;
		}
		Factory::$templateTree[$template][$key] = $value;
	}

	/**
	* Returns a template's tree given the template name.
	*
	* @param 	$template <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function getTemplateTree($template='') {
		return (isset(Factory::$templateTree[$template])) ? Factory::$templateTree[$template] : null;
	}

	/**
	* Returns an instance of Lite\Compiler.
	*
	* @param 	$template <String>
	* @param 	$encode <Boolean>
	* @param 	$skipFileToString <Boolean>
	* @access 	protected
	* @return 	Object
	*/
	protected function getCompiler($template, $encode=false, $skipFileToString=false) : Compiler {
		return new Compiler($this, $template, $encode, $skipFileToString);
	}

}