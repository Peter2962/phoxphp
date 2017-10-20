<?php
namespace Package\Http\Router;

class Config {

	/**
	* @param 	$file <String>
	* @param 	$key <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function get($file='', $key='') {
		if ('' == $file) {
			return;
		}
		$file = include 'src/Config/'.$file.'.php';
		if (gettype($file) !== 'array') {
			return;
		}
		return $file[$key] ?? $key;
	}

}