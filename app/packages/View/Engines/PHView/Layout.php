<?php
/**
* @package 	Package\PHView\Layout
* @author 	Peter Taiwo
*/

namespace Package\View\Engines\PHView;

use Package\View\Engines\PHView\View;

class Layout
{

	/**
	* @var 		$view
	* @access 	protected
	*/
	protected 	$view;

	/**
	* @var 		$layout
	* @access 	protected
	*/
	protected 	$layout;

	/**
	* @var 		$content
	* @access 	protected
	*/
	protected 	$content;

	/**
	* @var 		$viewOutput
	* @access 	protected
	*/
	protected 	$viewOutput;

	/**
	* @param 	$view Package\View\Engines\PHView\View
	* @param 	$layoutFile <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct(View $view, String $layoutFile, $viewOutput)
	{
		$this->view = $view;
		$this->layout = $view->getLayout();
	}

	/**
	* @access 	protected
	* @return 	String
	*/
	protected function content()
	{

	}

}