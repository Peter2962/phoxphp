<?php
/**
* @author 		Peter Taiwo <peter@phoxphp.com>
* @package 		App\BaseException
* @license 		MIT License
*
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

namespace App;

use Exception;
use RuntimeException;
use App\Exceptions\Contract\ExceptionContract;

abstract class BaseException extends Exception
{

	/**
	* @var 		$code
	* @access 	public
	*/
	public 		$code;

	/**
	* @var 		$message
	* @access 	public
	*/
	public 		$message;

	/**
	* @var 		$view
	* @access 	public
	*/
	public 		$view;

	/**
	* @access 	public
	* @return 	<void>
	*/
	public function __construct()
	{
		if (!$this instanceof ExceptionContract) {
			throw new RuntimeException(
				sprintf(
					'[%s] must implement [%s] interface',
					$this->getExceptionClass(),
					App\Exceptions\Contract\ExceptionContract::class	
				)
			);
		}
	}

	/**
	* Returns response code passed from child exception object.
	*
	* @access 	public
	* @return 	Integer
	*/
	public function getResponseCode() : int
	{
		return $this->code;
	}

	/**
	* Returns exception message passed from child exception object.
	*
	* @access 	public
	* @return 	String
	*/
	public function getExceptionMessage() : String
	{
		return $this->message;
	}

	/**
	* Returns view to be rendered.
	*
	* @access 	public
	* @return 	String
	*/
	public function getView() : String
	{
		return $this->view;
	}

}