<?php
########################################################
# This file is part of phoxphp framework template files.
########################################################
namespace Package\View\Engines\Lite\Mixins;

use RuntimeException;
use Package\View\Engines\Lite\Factory;
use Package\View\Engines\Lite\Compiler;
use Package\View\Engines\Lite\Resolver;
use Package\View\Engines\Lite\Mixins\Interfaces\MixinInterface;

trait Messages {

	/**
	* @param 	$key <String>
	* @access 	public
	* @return 	String
	*/
	public static function getMesasge($key='') {
		$messages = array(
			'ext-template-message' => "Template cannot contain any data. Use a block to add your data.",
			'block-size-message' => "Block size does not match container size.",
			'no-container-message' => "One or more blocks do not have a container"
		);
		return (isset($messages[$key])) ? $messages[$key] : "";
	}

}

class ExtendMixin implements MixinInterface {

	use Messages;

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
	* @var 		$containers
	* @access 	private
	*/
	private 	$containers;

	/**
	* @var 		$blocks
	* @access 	private
	*/
	private 	$blocks;

	/**
	* @var 		$skipFileToString
	* @access 	private
	*/
	private 	$skipFileToString;

	/**
	* @param 	$factory Lite\Factory
	* @param 	$skipFileToString <Boolean>
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
	* @access 	public
	* @return 	Boolean
	*/
	protected function hasExtend() {
		$template = ($this->skipFileToString == true) ? $this->template : $this->factory->getTemplateContent($this->template);
		$preg = preg_match_all("/@extend\((\"|')(.*?)(\"|')\)/", $template, $matches);
		if (!$preg) {
			return false;
		}
		$this->directives = $matches;
		return true;
	}

	/**
	* Checks if extended template has container directive.
	* @param 	$template <String>
	* @access 	protected
	* @return 	Boolean
	*/
	protected function parentHasContainer($template='', $search=false) {
		if (!preg_match_all("/@container\((\"|')(.*?)(\"|')\)/", $this->factory->getTemplateContent($template), $matches)) {
			return false;
		}
		$this->containers = $matches;
		return true;
	}

	/**
	* Checks if current template has block directive.
	* @param 	$template <String>
	* @access 	protected
	* @return 	Boolean
	*/
	protected function hasBlock($template='') {
		$template = ($this->skipFileToString == true) ? $this->template : $this->factory->getTemplateContent($this->template);
		if (!preg_match_all("/@block\((.*?)\)(.*?)@endblock/s", $template, $matches)) {
			return false;
		}
		$this->blocks = $matches;
		return true;
	}

	/**
	* @access 	public
	* @throws 	RuntimeException
	* @return 	Array
	*/
	public function getOutput() {
		$compiledArray = array();
		$store = array();
		if ($this->hasExtend()) {

			$leftQuote = $this->directives[1][0];
			$rightQuote = $this->directives[3][0];
			Resolver::resolveQuotes($leftQuote, $rightQuote);

			$templateFile = $this->directives[2][0];
			if (!file_exists($this->factory->getTemplateBuild($templateFile))) {
				throw new RuntimeException(sprintf("Compile error. %s template not found.", $templateFile));
			}

			Compiler::addCustomOutput($this->directives[0][0], "");

			// Since we are only allowing just a template to extend only one template,
			// we will do a check to see if the template contains more than one extend
			// directives.
			if (sizeof($this->directives[0]) > 1) {
				throw new RuntimeException(sprintf("Multiple extend directives found in %s", $templateFile));
			}

			$findAll = preg_match_all("/.*/s", htmlentities($this->factory->getTemplateContent($this->template)), $matches);

			if (!$this->hasBlock($this->template)) {
				if (preg_match_all("/.*/s", htmlentities($this->factory->getTemplateContent($this->template)), $matches)) {
					$body = $matches;
					// If the template is extending another template and it does not have the block
					// directives set and it also has data in it, we will throw an exception.
					if(trim($matches[0][0]) !== $this->directives[0][0]) {
						throw new RuntimeException(sprintf(Messages::getMesasge('ext-template-message')));	
					}
				}
				return;
			}

			// If the extended template does not have `@container` directive, we stop
			// the whole operation.
			if (!$this->parentHasContainer($templateFile) && $findAll) {
				throw new RuntimeException(sprintf(Messages::getMesasge('no-container-message')));
			}

			$blockTree = array();
			$blockLabels = array();

			if ($tree = $this->factory->getTemplateTree($this->template)) {

				$templateBlock = $tree['block:data'];
				foreach($this->blocks[0] as $i => $block) {
					$label = str_replace('"', '', $this->blocks[1][$i]);
					$blockLabels[] = $label;
					$blockTree[$label] = $this->blocks[2][$i];
				}			

				foreach($blockLabels as $block) {
					if (!in_array($block, $this->containers[2])) {
						throw new RuntimeException(sprintf(Messages::getMesasge('no-container-message')));
					}
				}

				foreach($this->containers[0] as $i => $container) {
					Resolver::resolveQuotes($this->containers[1][$i], $this->containers[3][$i]);
					$label = $this->containers[2][$i];
					Compiler::addCustomOutput($container, $this->factory->render($blockTree[$label], true, true));
				}

			}

			if (preg_match_all("/.*/s", htmlentities($this->factory->getTemplateContent($this->template)), $matches)) {
				$body = $matches;
			}

			return $this->factory->render($templateFile);
		}
	}

}