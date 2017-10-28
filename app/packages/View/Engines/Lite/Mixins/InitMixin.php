<?php
########################################################
# This file is part of phoxphp framework template files.
########################################################
namespace Package\View\Engines\Lite\Mixins;

use Package\View\Engines\Lite\Factory;
use Package\View\Engines\Lite\Compiler;
use Package\View\Engines\Lite\Mixins\Interfaces\MixinInterface;

class InitMixin implements MixinInterface {

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
	private 	$directives;

	/**
	* @var 		$skipFileToString
	* @access 	private
	*/
	private 	$skipFileToString;

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
	protected function hasInit() {
		$template = ($this->skipFileToString == true) ? $this->template : $this->factory->getTemplateContent($this->template);
		$preg = preg_match_all("/{{ set (.*?)\=(.*?)}}/s", $template, $matches);
		if (!$preg) {
			return false;
		}
		$this->directives = $matches;
		return true;
	}

	/**
	* @param 	$variableName <String>
	* @param 	$variableValue <String>
	* @access 	protected
	* @return 	String
	*/
	protected function read($variableName='', $variableValue='') {
		return '<?php $'.$variableName.' = '.$variableValue.'; ?>'; 
	}

	/**
	* @access 	public
	* @return 	void
	*/
	public function getOutput() {
		if ($this->hasInit()) {
			foreach($this->directives[0] as $i => $directive) {
				$directiveName = $this->directives[0][$i];
				$variableName = $this->directives[1][$i];
				$variableValue = $this->directives[2][$i];
				Compiler::addCustomOutput($directiveName, $this->read($variableName, $variableValue));
			}
		}
	}
	
}