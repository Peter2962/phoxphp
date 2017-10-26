<?php
namespace View\Block;

use View\Exceptions\TemplateNotFoundException;
use DependencyInjection\Injector\InjectorBridge;
use View\ArgResolver;
use FileSystem\File;
use App\Finder;

class Manager extends InjectorBridge {

	/**
	* @var 		$block
	* @access 	private
	*/
	private 	$block;

	/**
	* @param 	$block <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct($block='') {
		$this->block = $block;
	}

	/**
	* Checks if a block template exists.
	*
	* @access 	public
	* @return 	Boolean
	*/
	public function exists() {
		$finder = new Finder();
		$this->block = ArgResolver::getResolvedTemplatePath($finder->get('path.view.blocks').$this->block);

		$file = new File($this->block);
		if ($file->exists()) {
			return true;
		}
		return false;		
	}

	/**
	* @access 	public
	* @return 	void
	*/
	public function render() {
		if (!$this->exists()) {
			throw new TemplateNotFoundException(sprintf("Unable to render block template {%s}", $this->block));
		}

		$block = file_get_contents($this->block);
		eval("?> $block <?php ");
	}
	
}