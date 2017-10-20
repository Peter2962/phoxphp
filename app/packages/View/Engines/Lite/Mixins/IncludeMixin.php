<?php
########################################################
# This file is part of phoxphp framework template files.
########################################################
namespace Package\View\Engines\Lite\Mixins;

use RuntimeException;
use Package\View\Engines\Lite\Factory;
use Package\View\Engines\Lite\Compiler;
use Package\View\Engines\Lite\Mixins\Interfaces\MixinInterface;

class IncludeMixin implements MixinInterface {

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
	* @return 	Boolean
	*/
	protected function hasInclude() {
		$template = ($this->skipFileToString == true) ? $this->template : $this->factory->getTemplateContent($this->template);
		$preg = preg_match_all("/@include\(\'(.*?)\'\)/", $template, $matches);
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
		if ($this->hasInclude()) {
			foreach($this->directives[0] as $io => $output) {
				$templateFile = $this->directives[1][$io];
				if (!file_exists($this->factory->getTemplateBuild($templateFile))) {
					throw new RuntimeException(sprintf("Compile error. %s template not found.", $templateFile));
				}

				Compiler::addCustomOutput($output, $this->factory->render($templateFile, true));
			}
		}
		return $compiledArray;
	}

}