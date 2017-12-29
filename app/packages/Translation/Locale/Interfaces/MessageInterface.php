<?php
namespace Package\Translation\Locale\Interfaces;

use Translation\Factory;

interface MessageInterface {

	/**
	* Gets a message from properties file.
	*
	* @param 	$factory Translation\Factory
	* @param 	$parameters <Array>
	* @access 	public
	* @return 	String
	*/
	public function getMessage(Factory $factory, array $parameters=array());

}