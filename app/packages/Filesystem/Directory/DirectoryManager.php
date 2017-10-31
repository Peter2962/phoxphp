<?php
namespace Package\FileSystem\Directory;
/**
* @author 		Peter Taiwo
* @version 		1.0.0
* @package 		FileSystem.Directory
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
* SOFTWARE.
*/

use FileNotFoundException;
use Package\FileSystem\Converter;
use Package\FileSystem\File\Reader;
use Package\FileSystem\File\FileManager;
use Package\FileSystem\Permission\PermissionMaker;
use Package\FileSystem\Permission\Interfaces\Permittable;

class DirectoryManager implements Permittable
{

	/**
	* @var 		$directory
	* @access 	private
	*/
	private 	$directory=null;

	/**
	* @var 		$blockedList
	* @access 	private
	*/
	private static $blockedList=[];

	/**
	* @param 	$directory <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct($directory='')
	{
		(String) $this->directory=$directory;
	}

	/**
	* Creates a new directory
	*
	* @param 	$directory | <String>
	* @access 	public
	* @return 	void
	*/
	public function mkdir($directory='')
	{
		if (null === $this->directory) {
			$this->directory = $directory;
		}
		if (!function_exists('mkdir')) {
			return;
		}

		mkdir($this->directory, 0755, true);
	}

	/**
	* Creates multiple directories.
	*
	* @param 	$directories | <Array>
	* @access 	public
	* @return 	void
	*/
	public function mkdirs(array $directories=[])
	{
		$map = null;
		if (!empty($directories)) {
			$map = array_map([$this, 'mkdir'], $directories);
		}

		return $map;
	}

	/**
	* Checks if adirectory exists.
	*
	* @param 	$directory | <String>
	* @access 	public
	* @return 	void
	*/
	public function exists($directory='')
	{
		if (null===$this->directory) {
			$this->directory=$directory;
		}
		if (file_exists($this->directory) && is_dir($this->directory)) {
			return true;
		}

		return;
	}

	/**
	* Deletes a directory.
	*
	* @access 	public
	* @return 	void
	*/
	public function delete()
	{
		if (!$this->exists()) {
			throw new FileNotFoundException("Unable to delete directory. Directory does not exist.");
		}

		return rmdir($this->directory);
	}

	/**
	* Returns directories and files in a directory.
	*
	* @access 	public
	* @return 	Array
	*/
	public function getAll()
	{
		if ($this->exists() && $this->isReadable()) {
			return scandir($this->directory);
		}

		return [];
	}

	/**
	* Returns only the files in a directory.
	*
	* @access 	public
	* @return 	Array
	*/
	public function getFiles()
	{
		$files = [];
		if ($this->exists() && $this->isReadable()) {
			$directory = opendir($this->directory);
			while($opened = readdir($directory)) {
				if (is_file($this->directory . DIRECTORY_SEPARATOR . $opened) && !Directory::isBlockListed($opened)) {
					$files[] = $opened;
				}
			}

			closedir($directory);
		}

		return $files;
	}

	/**
	* Returns only the directories in a directory.
	*
	* @access 	public
	* @return 	Array
	*/
	public function getDirectories()
	{
		$dirs = [];
		if ($this->exists() && $this->isReadable()) {
			$dir = opendir($this->directory);
			while ($opened = readdir($dir)) {
				if (is_dir($this->directory . DIRECTORY_SEPARATOR . $opened) && !Directory::isBlockListed($opened)) {
					$dirs[] = $opened;	
				}
			}

			closedir($dir);
		}
		return $dirs;
	}

	/**
	* Checks if the directory has a specified file. If the file exists, an instance
	* of FileSystem\File is created using the checked file.
	*
	* @param 	$file <String>
	* @access 	public
	* @return 	Object \FileSystem\File
	*/
	public function hasFile($file='')
	{
		if (in_array((String) $file, $this->getAll())) {
			return new FileManager($this->directory.'/'.$file);
		}

		return false;
	}

	/**
	* Checks if the directory has a specified directory. If the directory is found, an instance
	* of FileSystem\Directory is returned on the current directory.
	*
	* @param 	$directory <String>
	* @access 	public
	* @return 	Object FileSystem\Directory | Boolean
	*/
	public function hasDir($directory='')
	{
		if (in_array((String) $directory, $this->getAll()) && is_dir($this->directory.DIRECTORY_SEPARATOR.$directory)) {
			return new Directory($this->directory.DIRECTORY_SEPARATOR.$directory);
		}

		return false;
	}

	/**
	* Checks if a directory is readable.
	*
	* @access 	public
	* @return 	void
	*/
	public function isReadable()
	{
		if (is_readable($this->directory)) return true; return;
	}

	/**
	* Adds a file or directory to the blockedlist.
	*
	* @param 	$list <Array>
	* @access 	public
	* @return 	void
	*/
	public static function addToBlockedList(array $list=[])
	{
		Directory::$blockedList=$list;
	}

	/**
	* Checks if a file or directory is block listed.
	*
	* @param 	$pack <String>
	* @access 	public
	* @return 	Boolean
	*/
	public static function isBlockListed($pack='')
	{
		return (in_array($pack, Directory::$blockedList)) ? true : false;
	}

	/**
	* @ {inheritDoc}
	* @access 	public
	*/
	public function getPermitted()
	{
		return $this->directory;
	}

	/**
	* Changes a file owner.
	*
	* @param 	$owner <String>
	* @access 	public
	* @return 	void
	*/
	public function chown($owner='')
	{
		if (!$this->exists()) {
			return;
		}

		return $this->permissionInstance()->changeOwner($this, $owner);
	}

	/**
	* Changes a file group.
	*
	* @param 	$group <String>
	* @access 	public
	* @return 	void
	*/
	public function chgrp($group='')
	{
		if (!$this->exists()) {
			return;
		}

		return $this->permissionInstance()->changeGroup($this, $group);
	}

	/**
	* Changes a file mode.
	*
	* @param 	$mode <String>
	* @access 	public
	* @return 	void
	*/
	public function chmod($mode='')
	{
		if (!$this->exists()) {
			return;
		}

		return $this->permissionInstance()->changeMode($this, $mode);
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getName()
	{
		return $this->directory;
	}

	/**
	* Returns an instance of PermissionMaker
	*
	* @access 	private
	* @return 	Object | PermissionMaker
	*/
	private function permissionInstance()
	{
		return new PermissionMaker();
	}	

}