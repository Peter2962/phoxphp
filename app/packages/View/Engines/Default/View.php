<?php
/**
* @author 		Peter Taiwo
* @version 		1.0.0
* @package 		View
* @copyright 	MIT License
* Copyright (c) 2017 PhoxPHP
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
*/

use DependencyInjection\Injector\InjectorBridge;
use View\Exceptions\TemplateNotFoundException;
use View\Template\Manager as TemplateManager;
use View\Interfaces\ViewAccessorInterface;
use View\Layout\Manager as LayoutManager;
use View\Block\Manager as BlockManager;
use View\Interfaces\ViewInterface;
use View\ArgResolver;
use FileSystem\File;
use View\Builder;

class View extends InjectorBridge implements ViewInterface, ViewAccessorInterface {

	/**
	* @var 		$layout
	* @access 	public
	*/
	public 		$layout = null;

	/**
	* @var 		$view
	* @access 	public
	*/
	public 		$view = null;

	/**
	* @var 		$variables
	* @access 	private
	*/
	private static $variables = [];

	/**
	* @var 		$layoutVariables
	* @access 	private
	*/
	private static $layoutVariables = [];

	/**
	* @param 		$template <String>
	* @access 		public
	* @return 		Boolean
	*/
	public function templateExists($template=null) {
		$templateManager = new TemplateManager($template);
		if ($templateManager->exists()) {
			return true;
		}
		return false;
	}

	/**
	* @param 		$block <String>
	* @access 		public
	* @return 		Boolean
	*/
	public function blockExists($block='') {
		$blockManager = new BlockManager($block);
		if ($blockManager->exists()) {
			return true;
		}
		return false;
	}

	/**
	* @param 		$layout <String>
	* @access 		public
	* @return 		Boolean
	*/
	public function layoutExists($layout='') {
		$layoutManager = new LayoutManager($layout);
		if ($layoutManager->exists()) {
			return true;
		}
		return false;
	}

	/**
	* @param 		$template <String>
	* @access 		private
	* @deprecated
	*/
	private function checkFile($template) {
		$file = new File($template);
		if ($file->exists()) {
			return true;
		}
		return false;
	}

	/**
	* Renders a view and layout which is optional. This method does not render
	* a view directly but passes the view and layout to the application object which 
	* then renders the view and layout using the layout manager and layout builder.
	*
	* @todo 		Create TemplateNotFoundException
	* @param 		$view | <String> | Required
	* @param 		$layout | <String> | Optional
	* @access 		public
	* @return 		void
	*/
	public function render($view='', $layout='') {
		$this->layout = $this->getDefaultLayout();

		$this->view = $view;
		if ('' !== $layout) $this->layout = $layout;

		$this->view = ArgResolver::getResolvedTemplatePath('public.views.templates.'.$this->view);

		// validate view template location.....
		try {
			$view = $this->fileSystem($this->view)->file();
			if (!$view->exists()) {
				throw new TemplateNotFoundException(sprintf("Unable to render template {%s}.", $this->view));
			}
		}catch(TemplateNotFoundException $t) {}

		$builder = new Builder($this);
		$viewOutput = htmlentities($builder->build());

		$layout = new LayoutManager($this->layout);

		View::$layoutVariables['block'] = $viewOutput;
		View::$layoutVariables['site_url'] = $this->load('config')->get('app','app_url');
		View::$layoutVariables['view_content'] = $viewOutput;

		$variables = array_merge($this->getLayoutVariables(), $this->getViewVariables());

		$values = array_values($variables);
		foreach(array_keys($variables) as $iterate => $val) {
			$$val = $values[$iterate];
		}

		$layoutView = $layout->render(View::$layoutVariables);
		$layoutView = html_entity_decode($layoutView);
		eval("?> $layoutView <?php ");
	}

	/**
	* @param 		$variable <String>
	* @param 		$value <Mixed>
	* @access 		public
	* @return 		void
	*/
	public function setVariable($variable='', $value='') {
		View::$variables[$variable] = $value;
	}

	/**
	* @param 	$param <String>
	* @param 	$value <Mixed>
	* @access 	public
	* @return 	void
	*/
	public function setLayoutVariables($param='', $value='') {
		View::$layoutVariables[$param] = $value;
	}

	/**
	* Returns the value of avariable only if it exists.
	*
	* @param 		$variable <Mixed>
	* @access 		public
	* @return 		Mixed
	*/
	public function getVariable($variable='') {
		if ($this->hasVariable($variable)) {
			return View::$variables[$variable];
		}
		return null;
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getLayoutVariables() {
		return View::$layoutVariables;
	}

	/**
	* @param 	$string <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function getLayoutVariable($variable='') {
		return View::$layoutVariables[$variable];
	}

	/**
	* @param 	$block String
	* @access 	public
	* @return 	void
	*/
	public function block($block='') {
		$manager = new BlockManager($block);
		return $manager->render();
	}

	/**
	* Checks if a variable exists.
	*
	* @param 		$variable <Mixed>
	* @access 		public
	* @return 		Boolean
	*/
	public function hasVariable($variable='') {
		if (isset(View::$variables[$variable])) return true;
		return false;
	}

	/**
	* @access 		public
	* @return 		String
	*/
	public function getDefaultLayout() {
		return $this->load('config')->get('app','default_layout');
	}

	/**
	* @param 		$param <String>
	* @access 		private
	* @return 		Object
	*/
	private function fileSystem($param) {
		return new FileSystem($param);
	}

	/**
	* @param 		$block <String>
	* @access 		private
	*/
	private function builder($block='') {
		return new Builder($this);
	}

	/**
	* @access 		public
	* @return 		String
	*/
	public function getView() {
		return $this->view;
	}	

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getViewVariables() {
		return View::$variables;
	}

}