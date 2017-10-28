<?php
namespace Package\View\Engines\Lite\Hooks;

use RuntimeException;
use Package\View\Engines\Lite\Formatter;
use Package\View\Engines\Lite\Factory;

class OnCompileStart {

	/**
	* @var 		$factory
	* @access 	private
	*/
	private 	$factory;

	/**
	* @var 		$skipFileToString
	* @access 	private
	*/
	private 	$skipFileToString;

	/**
	* @param 	$factory Lite\Factory
	* @param 	$skipFileToString <Boolean>
	* @access 	public
	* @return 	void
	*/
	public function __construct(Factory $factory, $skipFileToString=false) {
		$this->factory = $factory;
		$this->skipFileToString = $skipFileToString;
	}

	/**
	* @param 	$template <String>
	* @access 	public
	* @throws 	RuntimeException
	* @return 	void
	*/
	public function runHookOn($template='') {
		$this->checkSyntaxCode($template);
	}

	/**
	* @param 	$template <String>
	* @access 	private
	* @throws 	RuntimeException
	* @return 	void
	*/
	private function checkSyntaxCode($template='') {
		$templateContent = ($this->skipFileToString == true) ? $template : $this->factory->getTemplateContent($template);
		$disableDefaultCode = $this->factory->isCodeDisabled();
		$preg = preg_match("/<\?php(.*?)\?>/s", $templateContent);

		if ($preg && boolval($disableDefaultCode) == true) {
			throw new RuntimeException("PHP syntax detected in template.");
		}
	}

}