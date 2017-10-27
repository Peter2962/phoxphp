<?php
namespace Package\Translation\Locale;

use Translation\Exceptions\ResourceNotFoundException;
use Translation\Locale\Interfaces\MessageInterface;
use Translation\Locale\ResourceParser;
use Translation\Locale\Locale;
use Translation\Factory;
use RuntimeException;
use StdClass;

class Message implements MessageInterface {

	/**
	* @var 		$message
	* @access 	private
	*/
	private 	$message;

	/**
	* @var 		$locale
	* @access 	private
	*/
	private 	$locale;

	/**
	* @var 		$resourcePath
	* @access 	private
	*/
	private 	$resourcePath;

	/**
	* @var 		$resourceObject
	* @access 	private
	*/
	private 	$resourceObject;

	/**
	* Constructor
	*
	* @param 	$message <String>
	* @param 	$locale <Object> Translation\Locale
	* @access 	public
	* @return 	void
	*/
	public function __construct($message, Locale $locale) {
		$this->message = $message;
		$this->locale = $locale;
	}

	/**
	* @param 	$factory <Object> Translation\Factory
	* @param 	$parameters <Array>
	* @access 	private
	* @return 	void
	*/
	private function beforeMessageGet(Factory $factory, $parameters) {
		$locale = $this->locale->getLocale();
		$property = $locale.'.properties';

		$this->resourceObject = $this->resolveResourceFile($factory, $property, $parameters);
	}

	/**
	* @param 	$factory <Object> Translation\Factory
	* @param 	$resourceName <String>
	* @param 	$parameters <Array>
	* @access 	private
	* @return 	Boolean
	*/
	private function resolveResourceFile(Factory $factory, $resourceName, $parameters=[]) : StdClass {
		$propertyPath = $factory->getConfig('resources_path');
		if (!file_exists($propertyPath.$resourceName)) {
			throw new ResourceNotFoundException(sprintf("Resource file for %s not found", $resourceName));
		}

		$resource = new StdClass;
		$resource->region = str_replace('.properties', '', $resourceName);
		$resource->path = $propertyPath.$resourceName;
		$resource->data = htmlentities(file_get_contents($resource->path));
		$resource->tags = $parameters;

		return $resource;
	}

	/**
	* {@inheritDoc}
	*
	* @param 	$factory <Object> Translation\Factory
	* @param 	$parameters <Array>
	* @access 	public
	* @return 	String
	*/
	public function getMessage(Factory $factory, array $parameters=[]) {
		$this->beforeMessageGet($factory, $parameters);
		$parser = new ResourceParser($this->resourceObject);
		$resource = $parser->parseResource()->getResource();

		// If a message key does not exist, we either throw an error only if this is set in
		// the configuration file or just return the message key itself.

		if (!isset($resource[$this->message])) {
			if ($factory->getConfig('throw_missing_errors') == true) {
				throw new RuntimeException(sprintf("Unable to get message %s", $this->message));
			}
			return $this->message;
		}

		$message = (Object) $resource[$this->message];
		$messageTags = array();
		if (count($message->tags) > 0) {
			foreach($message->tags as $tag) {
				if (!array_key_exists($tag, $parameters)) {
					if ($factory->getConfig('throw_missing_errors') == true) {
						throw new RuntimeException(sprintf("Unable to get parameter %s", $tag));
					}
					break;
				}

				$messageTags['['.$tag.']'] = $parameters[$tag];
			}
		}

		return str_replace(array_keys($messageTags), array_values($messageTags), $message->data);
	}

}