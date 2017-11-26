<?php
/**
* @package 	PHView\View
* @author 	Peter Taiwo
*/

namespace Package\View\Engines\PHView;

use RuntimeException;
use Package\View\Engines\PHView\Config;
use Package\View\Engines\PHView\Layout;
use Package\View\Engines\PHView\Repository;
use Package\View\Engines\PHView\Block\Block;
use Package\View\Engines\PHView\Renderer\Renderer;
use Package\View\Engines\PHView\Contracts\PHViewContract;
use Package\View\Engines\PHView\Exceptions\FileNotFoundException;

class View implements PHViewContract
{

	/**
	* @var 		$layout
	* @access 	protected
	*/
	protected 	$layout = null;

	/**
	* @var 		$view
	* @access 	protected
	*/
	protected 	$view = null;

	/**
	* @var 		$variables
	* @access 	protected
	*/
	protected 	$variables;

	/**
	* @var 		$repository
	* @access 	protected
	*/
	protected 	$repository = 'default';

	/**
	* @var 		$content
	* @access 	protected
	*/
	protected 	$content;

	/**
	* @var 		$blocks
	* @access 	protected
	*/
	protected 	$blocks = [];

	/**
	* @var 		$block
	* @access 	protected
	*/
	protected 	$block;

	/**
	* @var 		$blockOpen
	* @access 	protected
	*/
	protected 	$blockOpen = false;

	/**
	* The construct accepts three arguments. $layout []
	*
	* @param 	$layout <String>
	* @param 	$view <String>
	* @param 	$variables <Array>
	* @access 	public
	* @return 	void
	*/
	public function __construct($layout='', $view='', $variables=[])
	{
		$this->view = $view;
		$this->layout = $layout;
		$this->variables = $variables;
	}

	/**
	* {@inheritDoc}
	*/
	public function setRepository(String $repository) : View
	{
		$this->repository = $repository;
		return $this;
	}

	/**
	* {@inheritDoc}
	*/
	public function setVariable(String $name, $value) : View
	{
		$this->variables[$name] = $value;
		return $this;
	}

	/**
	* {@inheritDoc}
	*/
	public function getVariable(String $name)
	{
		return $this->variables[$name] ?? null;
	}

	/**
	* {@inheritDoc}
	*/
	public function render($view='', $layout='') : View
	{
		if (sizeof(array_keys($this->variables)) > 0) {
			// We will only parse variables if any variable has been added.
			foreach(array_keys($this->variables) as $variable) {
				$$variable = $this->variables[$variable];
			}
		}

		if ($layout !== '') {
			$this->layout = $layout;
		}

		if ($view !== '') {
			$this->view = $view;
		}

		$repositoryDirectory = Config::get('repository_directory');
		$repository = new Repository($this);

		if (!$repository->hasLayouts()) {
			throw new FileNotFoundException(sprintf('Directory %s does not exist.', $repositoryDirectory . 'layouts'));
		}

		if (!$repository->hasViews()) {
			throw new FileNotFoundException(sprintf('Directory %s does not exist.', $repositoryDirectory . 'views'));
		}

		if ($this->view !== null && $this->view !== '') {
			$viewFile = $repository->getViewsPathWith($this->view, Config::get('extension'), true);
			$viewOutput = file_get_contents($viewFile);
			$this->content = $viewOutput;

			if ($this->layout == null || $this->layout == '') {
				eval("?> $viewOutput <?php ");
			}
		}

		if ($this->layout !== null && $this->layout !== '' && $this->view !== null && $this->view !== '') {
			$layoutFile = $repository->getLayoutsPathWith($this->layout, Config::get('extension'), true);
			$layoutOutput = file_get_contents($layoutFile);

			eval("?> $layoutOutput <?php ");
		}

		return $this;
	}

	/**
	* {@inheritDoc}
	*/
	public function setLayout($layout) : View
	{
		if (gettype($layout) !== 'string' || !$layout instanceof Layout) {
			throw new RuntimeException('Layout can either be string or instance of PHView\\Layout');
		}

		$this->layout = $layout;
		return $this;
	}

	/**
	* {@inheritDoc}
	*/
	public function setView(String $view) : View
	{
		return $this;
	}

	/**
	* {@inheritDoc}
	*/
	public function getView()
	{
		return $this->view;
	}

	/**
	* {@inheritDoc}
	*/
	public function getLayout()
	{
		return $this->layout;
	}

	/**
	* {@inheritDoc}
	*/
	public function getRepository() : String
	{
		return $this->repository;
	}

	/**
	* @access 	protected
	* @return 	String
	*/
	protected function content()
	{
		return $this->content;
	}

	/**
	* Renders a partial template.
	*
	* @param 	$partial <String>
	* @param 	$variables <Array>
	* @access 	protected
	* @return 	String
	*/
	protected function partial(String $partial, Array $variables=[])
	{
		if (sizeof(array_keys($variables)) > 0) {
			foreach(array_keys($variables) as $variable) {
				$$variable = $variables[$variable];
			}
		}

		$repository = new Repository($this);
		$partialFile = $repository->getPartialsPathWith($partial, Config::get('extension'), true);
		$partialOutput = file_get_contents($partialFile);

		eval("?> $partialOutput <?php ");
	}

}