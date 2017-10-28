<?php
namespace View;

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

use View\Interfaces\ViewAccessorInterface;
use RuntimeException;

class Builder {

	/**
	* @var 		$view
	* @access 	private
	*/
	private  	$view;

	/**
	* @var 		$variables
	* @access 	private
	*/
	private 	$variables;

	/**
	* @var 		$output
	* @access 	private
	*/
	private 	$output;

	/**
	* @var 		$requriedParams
	* @access 	private
	*/
	private static $requiredParams=[];

	/**
	* @var 		$requiredViewTags
	* @access 	private
	*/
	private static $requiredViewTags=[];

	/**
	* @var 		$queueViewContent
	* @access 	private
	*/
	private 	$queueViewContent=[];

	/**
	* @param 	ViewAccessorInterface $accessor
	* @access 	public
	* @return 	void
	*/
	public function __construct(ViewAccessorInterface $accessor) {
		$this->variables = $accessor->getViewVariables();
		$this->view = $accessor->getView();
	}

	/**
	* @access 	public
	* @return 	void
	*/
	public function build() {
		$values = array_values($this->variables);
		foreach(array_keys($this->variables) as $iterate => $variables) {
			$$variables = $values[$iterate];
		}

		$this->view = $this->getViewResource();
		$preg = preg_match_all("/{% (.*?) %}/", $this->view, $matches);

		// if (!empty(Builder::$requiredViewTags)) {	
		// 	foreach(Builder::$requiredViewTags as $tags) {
		// 		$tags = "{% $tags %}";
		// 		if (!$preg || !in_array($tags, $matches[0])) {
		// 			throw new RuntimeException("Unable to build view. Required tag not found.");
		// 		}
		// 	}
		// }

		if (!empty(Builder::$requiredParams)) {
			foreach(Builder::$requiredParams as $params) {
				if (!isset($this->variables[$params])) {
					exit("Unable to build view. Required parameter not found.");
				}
			}
		}

		if (!empty($this->queueViewContent)) {
			if ($preg) {
				$values = array_values($this->queueViewContent);
				foreach(array_keys($this->queueViewContent) as $iterate => $val){
					$parameter = $values[$iterate];
					
					if (!in_array($val, $matches[1]) || !in_array($parameter, $this->variables)) {
						exit("Unable to apply view content.");
					}

					$this->view = html_entity_decode($this->view);	
				}
			}
		}

		return $this->view;
	}

	/**
	* @param 	$tagName <String>
	* @param 	$parameter <String>
	* @access 	public
	* @return 	void
	*/
	public function applyViewContent($tagName='', $parameter='') {
		$this->queueViewContent[$tagName] = $parameter;
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getViewResource() {
		return file_get_contents($this->view);
	}

	/**
	* @param 	$tag <String>
	* @access 	public
	* @return 	void
	*/
	public static function setRequiredViewTag($tag='') {
		Builder::$requiredViewTags[] = $tag;
	}

	/**
	* @param 	$param <String>
	* @access 	public
	* @return 	void
	*/
	public static function setRequiredParam($param='') {
		Builder::$requiredParams[] = $param;
	}

}