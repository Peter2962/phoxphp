<?php
namespace View\Template;

use App\Component\AppComponent;
use App\Finder;
use View\ArgResolver;
use FileSystem\File;

class Manager {

	/**
	* @var 		$template
	* @access 	private
	*/
	private 	$template;

	/**
	* @param 	$template <String>
	* @access 	public
	*/
	public function __construct($template='') {
		$this->template = $template;
	}

	/**
	* Checks if a layout template exists.
	*
	* @access 	public
	* @return 	Boolean
	*/
	public function exists() {
		$template = ArgResolver::getResolvedTemplatePath($this->finder()->get('path.view.templates').$this->template);

		$file = new File($template);
		if ($file->exists()) {
			return true;
		}
		return false;
	}

	/**
	* @access 	private
	* @return 	Object
	*/
	private function finder() {
		return new Finder();
	}

}