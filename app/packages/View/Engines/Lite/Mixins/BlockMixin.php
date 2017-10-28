<?php
########################################################
# This file is part of phoxphp framework template files.
########################################################
namespace Package\View\Engines\Lite\Mixins;

use Package\View\Engines\Lite\Factory;
use Package\View\Engines\Lite\Compiler;
use Package\View\Engines\Lite\Resolver;
use Package\View\Engines\Lite\Parsers\FilterParser;
use Package\View\Engines\Lite\Mixins\Interfaces\MixinInterface;

class BlockMixin implements MixinInterface {

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
	* @var 		$blocks
	* @access 	private
	*/
	private 	$blocks;

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
	protected function hasBlock($template='') {
		$template = ($this->skipFileToString == true) ? $this->template : $this->factory->getTemplateContent($this->template);
		if (!preg_match_all("/@block\((\"|')(.*?)(\"|')\)(.*?)@endblock/s", $template, $matches)) {
			return false;
		}
		$this->blocks = $matches;
		return true;
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getOutput() {
		$compiledArray = array();
		$blockData = array();
		if ($this->hasBlock()) {

			foreach($this->blocks[0] as $i => $directive) {
				Resolver::resolveQuotes($this->blocks[1][$i], $this->blocks[3][$i]);

				$label = $this->blocks[2][$i];
				$content = $this->blocks[4][$i];
				$blockData['labels'][] = $label;
				$blockData['directives'][] = $directive;
				$blockData['content'][] = $content;

				// We are using this method to resolve conflicts when blocks have the same data.
				$this->factory->pushToTemplateTree($this->template, 'block:data', $blockData);
				// Deleting block from template....
				Compiler::addCustomOutput($directive, "");
			}
		}
		return $compiledArray;
	}

}