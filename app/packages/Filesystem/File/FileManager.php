<?php
namespace Package\FileSystem\File;
/**
* @author 		Peter Taiwo
* @version 		1.0.0
* @package 		FileSystem.File
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

use Package\FileSystem\Converter;
use Package\FileSystem\File\Reader;
use Package\FileSystem\File\Writer;
use Package\FileSystem\Permission\PermissionMaker;
use Package\FileSystem\Exceptions\FileNotFoundException;
use Package\FileSystem\Permission\Interfaces\Permittable;

class FileManager implements Permittable
{

	/**
	* @var 		$file
	* @access 	private
	*/
	private 	$file=null;

	/**
	* Constructor
	* @param 	$file <String>
	* @access 	public
	* @return 	$this
	*/
	public function __construct($file='')
	{
		(String) $this->file = $file;
		return $this;
	}

	/**
	* Creates a new file and returns file object afterwards.
	*
	* @access 	public
	* @return 	Object | $this
	*/
	public function create()
	{
		$file=['file' => $this->file];
		$setPointer = fopen($this->file, 'w');
		if (false == $setPointer) {
			touch($this->file);
		}

		return $this;
	}

	/**
	* This method does the same thing with File::create but it checks if the file exists
	* or not before attempting to create the file.
	*
	* @access 	public
	* @return void
	*/
	public function createIfNotExist()
	{
		if (!$this->exists()) {
			return $this->create();
		}

		return $this;
	}

	/**
	* Checks if a file exists.
	* @access 	public
	* @return 	Boolean
	*/
	public function exists()
	{
		$response = false;
		if (file_exists($this->file) && is_file($this->file)) {
			$response = true;
		}

		return $response;
	}

	/**
	* Returns the size of a file.
	*
	* @param 	$file <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function getFileSize($file='')
	{
		$response = false;
		if (null == $this->file) {
			$this->file=$file;
		}

		if ($this->exists()) {
			$size=filesize($this->file);
			return $size;
		}
	}

	/**
	* Gets the file of the specified file.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public function getFileType()
	{
		if ($this->exists()) {
			return filetype($this->file);
		}

		return null;
	}

	/**
	* Copies a file to a new directory.
	*
	* @param 	$newDestination <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function moveTo($newDestination='')
	{
		if ($this->exists() && false == file_exists($newDirectory)) {
			copy($this->file, $newDestination);
			return true;
		}

		return null;
	}

	/**
	* Renames the file to the string name returned in @param $newName.
	*
	* @param 	$newName <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function rename($newName='')
	{
		if ('' == $newName) {
			$newName = uniqid();
		}

		if ($this->exists()) {
			rename($this->file, $newName);
			return true;
		}
	}

	/**
	* Deletes a file.
	*
	* @param 	$file <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function delete($file='')
	{
		if (null == $this->file) {
			$this->file = $file;
		}

		unlink($this->file);
	}

	/**
	* Deletes multiple files.
	*
	* @param 	$files <Array>
	* @access 	public
	* @return 	void
	*/
	public function deleteMultiple(array $files=[])
	{
		if (sizeof($files) > 0) {
			return array_map([$this, 'deleteIfExists'], $array);
		}
	}

	/**
	* Delets a file only if it exists.
	*
	* @param 	$file <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function deleteIfExists($file='')
	{
		if ($this->exists()) {
			$this->delete($file);
			return true;
		}

		return null;
	}

	/**
	* Gets the modification time of a file.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public function getModifiedTime()
	{
		if ($this->exists()) { 
			return filemtime($this->file);
		}

		return null;
	}

	/**
	* Gets the last access time of the file.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public function getLastAccessTime()
	{
		if ($this->exists()) {
			return fileatime($this->file);
		}

		return null;
	}

	/**
	* Returns the name of file we are working with mainly because the property $file
	* is given a private access.
	*
	* @access 	public
	* @return 	String
	*/
	public function getFile()
	{
		return $this->file;
	}

	/**
	* Gets the content of a file.
	*
	* @param 	$file <String>
	* @access 	public
	* @return 	String
	*/
	public function read($file='')
	{
		if ('' !== $file) {
			$this->file = $file;
		}

		if ($this->exists()) {
			return Reader::read($this);
		}
	}

	/**
	* @todo 	Create documentation
	* @param 	$file
	* @access 	public
	* @return 	Array
	*/
	public function readRaw($file='')
	{
		if ('' !== $file) {
			$this->file = $file;
		}

		if ($this->exists()) {
			return Reader::readAsArray($this);
		}
	}

	/**
	* Checks if a file is readable.
	*
	* @param 	$file <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function isWritable($file='')
	{
		(Boolean) $response = false;
		if (null == $this->file) {
			$this->file = $file;
		}

		if (is_writable($this->file)) {
			$response = true; 
		}

		return $response;
	}

	/**
	* Checks if a file is executable.
	*
	* @param 	$file <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function isExecutable($file='')
	{
		(Boolean) $response = false;
		if (null == $this->file) {
			$this->file = $file;
		}

		if (is_executable($this->file)) {
			$response = true;
		}

		return $response;
	}

	/**
	* Writes data into file.
	*
	* @param 	$data <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function write($data='')
	{
		if (!$this->exists()) {
			return;
		}

		$writer = new Writer($this);
		return ($writer->write($data)) ? true : false;
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
	* Gets a line from the specified line.
	*
	* @param 	$line <Integer>
	* @access 	public
	* @throws 	FileNotFoundException
	* @return 	Mixed
	*/
	public function getLine($line=0)
	{
		if (!$this->exists()) {
			throw new FileNotFoundException("Unable to get line from file $this->file");
		}

		$reader = Reader::readAsArray($this);
		return $reader[$line];
	}

	/**
	* Checks if file was uploaded via http post.
	*
	* @access 	public
	* @return 	Boolean
	*/
	public function isPostedUpload()
	{
		if (!is_uploaded_file($this->file)) {
			return false;
		}

		return true;
	}

	/**
	* Returns the real path of file.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public function getRealPath()
	{
		if ($this->exists()) {
			return realpath($this->file);
		}

		return false;
	}

	/**
	* Returns the permitted file that is being used.
	*
	* @access 	public
	* @return 	String
	*/
	public function getPermitted()
	{
		return $this->file;
	}

	/**
	* Returns an instance of PermissionMaker.
	*
	* @access 	private
	* @return 	Object FileSystem\Permission\PermissionMaker
	*/
	private function permissionInstance()
	{
		return new PermissionMaker();
	}

}