<?php
########################################################
# This file is part of phoxphp framework template files.
########################################################
namespace Package\View\Engines\Lite\Mixins;

use Package\View\Engines\Lite\Compiler;
use Package\View\Engines\Lite\Factory;
use Package\View\Engines\Lite\Mixins\Interfaces\MixinInterface;

class EachMixin implements MixinInterface {

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
	* @var 		$bodyConditionalStatements
	* @access 	private
	*/
	private 	$bodyConditionalStatements = array('@else', '@elseif', '@end');

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
	* Checks if the template has `each` directive.
	* @access 	protected
	* @return 	Boolean
	* @todo 	Fix whitespace after if statement
	*/
	protected function hasFor() {
		$template = ($this->skipFileToString == true) ? $this->template : $this->factory->getTemplateContent($this->template);
		$preg = preg_match_all("/@each\((.*)\)/", $template, $matches);
		if (!$preg) {
			return false;
		}

		$this->directives = $matches;
		return true;
	}

	/**
	* @param 	$statement <String>
	* @access 	protected
	* @return 	String
	*/
	protected function read($statement='') {
		return '<?php foreach('.$statement.'): ?>';
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getOutput() {
		$compiledArray = array();
		if ($this->hasFor()) {
			foreach($this->directives[0] as $i => $directive) {
				$directiveCode = $this->directives[1][$i];
				Compiler::addCustomOutput($directive, htmlentities($this->read($directiveCode)));
				Compiler::addCustomOutput('@endeach', htmlentities('<?php endforeach; ?>'));
			}
		}

		return $compiledArray;
	}
	
}