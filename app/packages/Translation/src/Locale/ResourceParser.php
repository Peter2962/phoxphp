<?php
namespace Package\Translation\Locale;

use StdClass;
use Package\Translation\Exceptions\BadConfigurationSourceException;
use Package\Translation\Locale\Interfaces\ResourceParserInterface;

class ResourceParser implements ResourceParserInterface {

	/**
	* @var 		$resource
	* @access 	private
	*/
	private 	$resource;

	/**
	* @var 		$parsedResource
	* @access 	private
	*/
	private 	$parsedResource;

	/**
	* @param 	$resource <Object>
	* @access 	public
	* @return 	void
	*/
	public function __construct(StdClass $resource) {
		$this->resource = $resource;
		$this->parsedResource = new StdClass;
	}

	/**
	* {@inheritDoc}
	*
	* @access 	public
	* @return 	Object
	*/
	public function parseResource() : ResourceParser {
		$data = $this->resource->data;
		$dataLayer = explode("\n", $data);
		$parsedResource = array();

		foreach($dataLayer as $key) {
			$key = explode(':', $key);
			if (sizeof($key) < 2) {
				throw new BadConfigurationSourceException(sprintf('Error getting message from %s. Invalid message formatting.', $this->resource->path));
			}

			$parsedResource[$key[0]] = array('data' => $this->stripIndexSpace($key[1]), 'tags' => $this->getParameters($key[1]));
		}

		$this->parsedResource = $parsedResource;
		return $this;
	}

	/**
	* {@inheritDoc}
	*
	* @access 	public
	* @return 	Array
	*/
	public function getResource() : Array {
		return $this->parsedResource;
	}

	/**
	* Checks if string starts with space and removes it if it does.
	*
	* @param 	$string <String>
	* @access 	private
	* @return 	String
	*/
	private function stripIndexSpace($string='') {
		$string = explode(' ', $string);
		$stringArray = array();
		foreach($string as $str) {
			if ($str !== '') {
				$stringArray[] = $str;
			}
		}
		return implode(' ', $stringArray);
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	Array
	*/
	private function getParameters($string='') {
		if(preg_match_all("/\[(.*?)\]/", $string, $match)) {
			return $match[1];
		}
	}

}