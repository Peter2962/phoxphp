<?php
########################################################
# This file is part of phoxphp framework template files.
########################################################
namespace Package\View\Engines\Lite\Mixins;

use Package\View\Engines\Lite\Factory;
use Package\View\Engines\Lite\Compiler;
use Package\View\Engines\Lite\Parsers\VariableParser;
use Package\View\Engines\Lite\Parsers\FilterParser;
use Package\View\Engines\Lite\Mixins\Interfaces\MixinInterface;

class OutputMixin implements MixinInterface {

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
	* Returns an instance of Lite\FilterParser 
	* @access 	public
	* @return 	Object
	*/
	protected function filter() : FilterParser {
		return new FilterParser($this->factory);
	}

	/**
	* Returns an instance of VariableParser
	* @access 	protected
	* @return 	Object
	*/
	protected function variableParser() : VariableParser {
		return new VariableParser($this->factory);
	}

	/**
	* @access 	protected
	* @return 	Boolean
	*/
	protected function hasOutput() {
		$template = ($this->skipFileToString == true) ? $this->template : $this->factory->getTemplateContent($this->template);
		$preg = preg_match_all("/{{(.*?)}}/s", $template, $matches);
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
		$filteredOutput = $this->filter()->apply($variable);
		return '<?php echo '.$this->variableParser()->apply($filteredOutput).'; ?>';
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getOutput() {
		$compiledArray = array();
		if ($this->hasOutput()) {
			foreach($this->directives[0] as $i => $directive) {
				Compiler::addCustomOutput($directive, htmlentities($this->read($this->directives[1][$i])));
			}
		}
		return $compiledArray;
	}

}