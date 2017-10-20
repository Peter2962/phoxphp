<?php
namespace View\Layout;

use App\Component\AppComponent;
use View\Interfaces\ViewAccessorInterface;
use App\Finder;
use View\ArgResolver;
use FileSystem\File;
use View\Builder;
use Config;

class Manager implements ViewAccessorInterface {

	/**
	* @var 		$layoutSectionTag
	* @access 	private
	*/
	private 	$layoutSectionTag="{% block %}";

	/**
	* @var 		$layout
	* @access 	private
	*/
	private  	$layout;

	/**
	* @var 		$variables
	* @access 	private
	*/
	private 	$variables=[];

	/**
	* @param 	$layout <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct($layout='') {
		$this->layout=$layout;
	}

	/**
	* @access 	public
	* @return 	Boolean
	*/
	public function exists() {
		$layout=ArgResolver::getResolvedTemplatePath($this->finder()->get('path.view.layouts').$this->layout);

		$file=new File($layout);
		if ($file->exists()) {
			return true;
		}

		return false;
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getResourceLocation() {
		return ArgResolver::getResolvedTemplatePath($this->finder()->get('path.view.layouts').$this->layout);
	}

	/**
	* Returns the layout name.
	* @access 	public
	* @return 	String
	*/
	public function getLayoutName() {
		return $this->layout;
	}

	/**
	* @param 	$parameters <Array>
	* @access 	public
	* @return 	void
	*/
	public function render(array $parameters=[]) {
		$this->variables = $parameters;
		$this->layout = $this->getResourceLocation();		
		
		$builder = new Builder($this);

		if (isset($this->variables['block'])) {
			Builder::setRequiredParam('block');
			Builder::setRequiredViewTag('block');
		}

		$builder->applyViewContent('block', $this->variables['block']);

		return $viewOutput = htmlentities($builder->build());
	}

	/**
	* @access 	private
	* @return 	Object
	*/
	private function finder() {
		return new Finder();
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getViewVariables() {
		return $this->variables;
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getView() {
		return $this->layout;
	}

}