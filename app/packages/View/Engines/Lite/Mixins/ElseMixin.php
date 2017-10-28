<?php
#########################################
# This file is part of phoxphp framework.
#########################################
namespace Package\View\Engines\Lite\Mixins;

use Package\View\Engines\Lite\Factory;
use Package\View\Engines\Lite\Compiler;
use Package\View\Engines\Lite\Mixins\Interfaces\MixinInterface;

class ElseMixin implements MixinInterface {

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
	* Checks if the template has `if` directive.
	* @access 	protected
	* @return 	Boolean
	*/
	protected function hasElse() {
		$template = ($this->skipFileToString == true) ? $this->template : $this->factory->getTemplateContent($this->template);
		$preg = preg_match_all("/@else@/s", $template, $matches);
		if (!$preg) {
			return false;
		}
		$this->directives = $matches;
		return true;
	}

	/**
	* @param 	$directive <String>
	* @access 	protected
	* @return 	String
	*/
	protected function read($directive='') {
		return '<?php else: ?>';
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getOutput() {
		$compiledArray = array();

		if ($this->hasElse()) {
			foreach($this->directives[0] as $i => $directive) {
				$directiveCode = $this->directives[0][$i];
				Compiler::addCustomOutput($directive, htmlentities($this->read($directiveCode)));
			}
		}

		return $compiledArray;
	}
	
}