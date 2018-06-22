<?php
/**
* @author 		Peter Taiwo <peter@phoxphp.com>
* @package 		App\ExceptionHandler
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

use App\Exceptions\Contract\ExceptionContract;

class ExceptionHandler
{

	/**
	* Handles thrown exception.
	*
	* @access 	public
	* @return 	<void>
	*/
	public function handleException()
	{
		$thrownObject = $this->getThrownObject();
		$view = 'exception';
		$responseCode = 500;

		if ($thrownObject instanceof ExceptionContract) {
			$view = $thrownObject->getView();
			$responseCode = $thrownObject->getResponseCode();
		}

		return $this->send(
			$thrownObject,
			$thrownObject->getMessage(),
			$view,
			$responseCode
		);
	}

	/**
	* Returns the thrown exception object.
	*
	* @access 	protected
	* @return 	Object
	*/
	protected function getThrownObject()
	{
		return debug_backtrace()[1]['args'][0];
	}

	/**
	* Sends the exception output to the client.
	*
	* @param 	$exception <Object>
	* @param 	$message <String>
	* @param 	$view <String>
	* @param 	$responseCode <Integer>
	* @access 	protected
	* @return 	<void>
	*/
	protected function send($exception, $message, $view, $responseCode)
	{
		$viewManager = app()->load('view');
		$viewManager->setVariable('exceptionName', get_class($exception));
		$viewManager->setVariable('devMode', config('app')->get('devMode'));
		$viewManager->setVariable('errorLine', $exception->getLine());
		$viewManager->setVariable('errorFile', $exception->getFile());
		$viewManager->setVariable('file', $exception->getFile());
		$viewManager->setVariable('line', $exception->getLine());
		$viewManager->setVariable('message', $exception->getMessage());
		$viewManager->render('errors/' . $view);

		http_response_code($responseCode);
	}

}