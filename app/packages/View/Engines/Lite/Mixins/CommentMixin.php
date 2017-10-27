<?php
########################################################
# This file is part of phoxphp framework template files.
########################################################
namespace Package\View\Engines\Lite\Mixins;

use Package\View\Engines\Lite\Mixins\Interfaces\MixinInterface;
use Package\View\Engines\Lite\Parsers\FilterParser;
use Package\View\Engines\Lite\Compiler\Compiler;
use Package\View\Engines\Lite\Factory;

class CommentMixin implements MixinInterface {

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
	* @param 	$skipFileToString <Boolean>
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
	* @access 	public
	* @return 	Object
	*/
	protected function filter() : FilterParser {
		return new FilterParser($this->factory);
	}

	/**
	* @access 	protected
	* @return 	Boolean
	*/
	protected function hasOutput() {
		$template = ($this->skipFileToString == true) ? $this->template : $this->factory->getTemplateContent($this->template);

		$preg = preg_match_all("/{#(.*?)#}/", $template, $matches);
		if (!$preg) {
			return false;
		}
		$this->directives = $matches;
		return true;
	}

	/**
	* @param 	$variable <String>
	* @access 	protected
	* @return 	String
	*/
	protected function read($variable='') {
		$filteredOutput = $this->filter()->getFilteredResult($variable);
		return '<?php /*'.$filteredOutput.'; */ ?>';
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getOutput() {
		$compiledArray = array();
		if ($this->hasOutput()) {
			foreach($this->directives[0] as $i => $directive) {
				Compiler::addCustomOutput($directive, $this->factory->render($this->read($this->directives[1][$i]), true, true));
			}
		}
		return $compiledArray;
	}

}