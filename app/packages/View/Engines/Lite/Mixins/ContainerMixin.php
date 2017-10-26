<?php
########################################################
# This file is part of phoxphp framework template files.
########################################################
namespace Package\View\Engines\Lite\Mixins;

use Package\View\Engines\Lite\Factory;
use Package\View\Engines\Lite\Compiler;
use Package\View\Engines\Lite\Parsers\FilterParser;
use Package\View\Engines\Lite\Mixins\Interfaces\MixinInterface;

class ContainerMixin implements MixinInterface {

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
	* @var 		$containers
	* @access 	private
	*/
	private 	$containers;

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
	* Checks if current template has block directive.
	* @param 	$template <String>
	* @access 	protected
	* @return 	Boolean
	*/
	protected function hasContainer($template='') {
		$template = ($this->skipFileToString == true) ? $this->template : $this->factory->getTemplateContent($this->template);
		if (!preg_match_all("/@container\(\"(.*?)\"\)/s", $template, $matches)) {
			return false;
		}
		$this->containers = $matches;
		return true;
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getOutput() {
		$compiledArray = array();
		$containerData = array();
		if ($this->hasContainer()) {
			foreach($this->containers[0] as $i => $directive) {
				$label = $this->containers[1][$i];
				$containerData['labels'][] = $label;
				$containerData['directives'][] = $directive;

				$this->factory->pushToTemplateTree('global', 'container:data', $containerData);
			}
		}
		return $compiledArray;
	}

}