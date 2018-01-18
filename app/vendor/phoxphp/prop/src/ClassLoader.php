<?php
namespace Kit\Prop;

use StdClass;
use ReflectionCLass;
use ReflectionMethod;
use ReflectionException;

class ClassLoader
{

	/**
	* Returns an instance of a class.
	*
	* @param 	$class <Object>
	* @param 	$arguments <Array>
	* @access 	public
	* @return 	Mixed
	*/
	public function getInstanceOfClass($class, ...$arguments)
	{
		return $this->callClassMethod($class, '__construct', $arguments);
	}

	/**
	* Calls a method of a class object.
	*
	* @param 	$class Object
	* @param 	$method <String>
	* @param 	$arguments <Array>
	* @access 	public
	* @return 	Mixed
	*/
	public function callClassMethod($class, $method, ...$arguments)
	{
		$resolvedParameters = [];
		$reflectedClass = new ReflectionCLass($class);
		if (!$reflectedClass->hasMethod($method)) {
			throw new ReflectionException('Call to undefined method ' . $method);
		}

		$methodName = $method;
		$method = $reflectedClass->getMethod($method);
		$methodParameters = $method->getParameters();

		if (count($methodParameters) < 1) {
			return $method;
		}

		$resolvedParameters = array_map(function($param) use ($reflectedClass) {
			$type = $param->getType();

			if (preg_match("/(.*?)\\\/", $type)) {
				$type = (String) $type;
				return new $type;
			}

			if (!$param->isDefaultValueAvailable()) {
				return $param = '';
			}

			return $param->getDefaultValue();
			
		}, $methodParameters);

		$resolvedParameters = array_filter($resolvedParameters);
		$resolvedParameters = array_merge($resolvedParameters, $arguments);

		if ($methodName == '__construct') {
			return $reflectedClass->newInstanceArgs($resolvedParameters);
		}

		return $method->invokeArgs($class, $resolvedParameters);
	}

}