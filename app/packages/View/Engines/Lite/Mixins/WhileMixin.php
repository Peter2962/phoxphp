<?php
########################################################
# This file is part of phoxphp framework template files.
########################################################
namespace Package\View\Engines\Lite\Mixins;

use Package\View\Engines\Lite\Compiler;
use Package\View\Engines\Lite\Factory;
use Package\View\Engines\Lite\Mixins\Interfaces\MixinInterface;

class WhileMixin implements MixinInterface {

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
	* Checks if the template has `while` directive
	* @access 	protected
	* @return 	Boolean
	*/
	protected function hasWhile() {
		$template = ($this->skipFileToString == true) ? $this->template : $this->factory->getTemplateContent($this->template);
		$preg = preg_match_all("/(@while|@w)\((.*?)\)(.*?)/s", $template, $matches);
		if (!$preg) {
			return false;
		}
		$this->directives = $matches;
		return true;
	}

	/**
	* @param 	$leftAssignment <String>
	* @param 	$body <String>
	* @access 	protected
	* @return 	String
	*/
	protected function read($leftAssignment='', $body='') {
		return '<?php while('.$leftAssignment.'): ?>'.$body;
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getOutput() {
		$compiledArray = array();
		if ($this->hasWhile()) {	
			foreach($this->directives[0] as $i => $directive) {
				$directiveDefault = $this->directives[0][$i];
				$conditionalBody = $this->directives[2][$i];
				$loopBody = $this->directives[3][$i];
				Compiler::addCustomOutput($directiveDefault, htmlentities($this->read($conditionalBody, $loopBody)));
				Compiler::addCustomOutput('@endwhile', htmlentities('<?php endwhile; ?>'));
			}
		}
		return $compiledArray;
	}
	
}