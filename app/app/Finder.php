<?php
/**
* MIT License
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:

* The above copyright notice and this permission notice shall be included in all
* copies or substantial portions of the Software.

* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
*/

/**
* @author 	Peter Taiwo
* @package 	App\Finder
*/

namespace App;

use Kit\FileSystem\File\FileManager;

class Finder
{

	const DS 	= DIRECTORY_SEPARATOR; 

	/**
	* @var 		$finder
	* @access 	private
	*/
	private 	$finder;

	/**
	* @access 	public
	* @return 	void
	*/
	public function __construct()
	{
		$file = new FileManager('public/config/finder.php');

		if (!$file->exists()) {

			trigger_error('Unable to load finder file.');
		
		}

		$this->finder = include $file->getFile();
	}

	/**
	* @param 	$path String
	* @access 	public
	* @return 	Mixed
	*/
	public function get($path='')
	{
		return (isset($this->finder[$path])) ? $this->finder[$path] : null;
	}

}