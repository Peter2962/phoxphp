<?php
#########################################################
# This file is part of phoxphp framework template files.
#########################################################
# -------------------------------------------------------
# Filters are assigned to template directives by adding
# a colon after the directive output like so:
# {{ 'text:toUpper' }}
# A group of filters can also be assigned to a directive
# output like so: {{ 'text::[toUpper, toLower]' }}
#########################################################
namespace Package\View\Engines\Lite\Parsers;

use RuntimeException;
use Package\View\Engines\Lite\Factory;
use Package\View\Engines\Lite\Storage\Filters;
use Package\View\Engines\Lite\Parsers\Interfaces\ParserInterface;

trait Messages {

	/**
	* @param 	$offset <String>
	* @access 	public
	* @return 	String
	*/
	public static function getMessage($offset='') {
		$messages = array(
			'apply-filter-message' => 'Unable to apply filter to %s. To add multiple filters use ::[filter_1, filter_2]',
			'filter-list-message' => 'Filter `%s` does not exist. Create a filter in src/Storage/Filters.php',
			'param-count-message' => 'Error while parsing filter %s arguments.'
		);
		return (isset($messages[$offset])) ? $messages[$offset] : '';
	}

}

class FilterParser implements ParserInterface {

	/**
	* @var 		$filter
	* @access 	private
	*/
	private 	$filter;

	/**
	* @var 		$groupedFilter
	* @access 	private
	*/
	private 	$groupedFilter;

	/**
	* @var 		$factory
	* @access 	private
	*/
	private 	$factory;

	/**
	* @var 		$parameters
	* @access 	private
	*/
	private 	$parameters;

	/**
	* @access 	public
	* @return 	void
	*/
	public function __construct(Factory $factory) {
		$this->factory = $factory;
		$this->filterList = new Filters();
	}

	/**
	* @param 	$filter <String>
	* @access 	private
	* @throws 	RuntimeException
	* @return 	void
	*/
	private function validateFilter($filter='') {
		if (!method_exists($this->filterList, $this->getFilterName($filter))) {
			throw new RuntimeException(sprintf(Messages::getMessage('filter-list-message'), $filter));
		}
	}

	/**
	* @param 	$filter <String>
	* @access 	private
	*/
	private function getFilterName($filter='') {
		return str_replace([' ', "'", '"'], '', $filter);
	}

	/**
	* Checks if a string has filter.
	* @param 	$string <String>
	* @access 	private
	* @return 	Boolean
	*/
	private function hasFilter($string='') {
		$preg = preg_match_all("/(.*?):(.*)/s", $string, $matches);
		if (!$preg || $this->hasGroupedFilter($string)) {
			return false;
		}
		$this->filter = $matches;
		return true;
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	Boolean
	*/
	private function hasParameter($string='') {
		if (!preg_match_all("/(.*)=(.*)/", $string, $matches)) {
			return false;
		}
		$this->parameters = $matches;
		return true;
	}

	/**
	* Checks if a string has grouped filters.
	* @param 	$string <String>
	* @access 	private
	* @return 	Boolean
	*/
	private function hasGroupedFilter($string='') {
		$preg = preg_match_all("/(.*?)::(.*?)/s", $string, $matches);
		if (!$preg) {
			return false;
		}
		$this->groupedFilter = $matches;
		return true;
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @throws 	RuntimeException
	* @return 	String
	*/
	private function applyFilter($string='') {
		$filter = $this->filter;
		$test = preg_match_all("/:/", $string, $match);
		// Because adding multiple filters with ':' is not supported, we do a check to
		// see if there are multiple colons in the string.
		if (sizeof($match[0]) > 1 || $this->hasGroupedFilter($string)) {
			throw new RuntimeException(sprintf(Messages::getMessage('apply-filter-message'), $string));
		}

		$filter = $filter[2][0];

		$this->validateFilter($filter);
		$filter = $this->getFilterName($filter);
		return $this->filterList->$filter(str_replace(':'.$filter, '', $string));
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	String
	* @deprecated
	*/
	private function applyGroupedFilter($string='') {}

	/**
	* @param 	$string <String>
	* @access 	public
	* @return 	String
	*/
	public function apply($string=null) {
		if ($this->hasFilter($string)) {
			return $this->applyFilter($string);
		}

		return $string;
	}

}