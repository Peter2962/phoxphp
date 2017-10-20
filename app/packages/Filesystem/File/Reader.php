<?php
namespace Package\FileSystem\File;

/**
* @author 	Peter Taiwo
* @package 	FileSystem.File.Reader
*/

use Package\FileSystem\File\FileManager;

class Reader {

	/**
	* Returns the content of a file.
	*
	* @param 	$file FileSystem\File
	* @access 	public
	* @return 	String
	*/
	public static function read(FileManager $file) {
		return file_get_contents($file->getFile());
	}

	/**
	* @param 	$file FileSystem\File
	* @access 	public
	* @return 	Array
	*/
	public static function readAsArray(FileManager $file) {
		return file($file->getFile());
	}

}