<?php
/**
* @author 		Peter Taiwo
* @version 		1.0.0
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

$baseDir = realpath(dirname(__FILE__));

define('DS', DIRECTORY_SEPARATOR);
define('BASEDIR', $baseDir);
define('BOOTSTRAP', BASEDIR . DS . 'app' . DS . 'bootstrap.php');
define('AUTOLOADER', BASEDIR . DS . 'app' . DS . 'autoloader' . DS . 'autoload.php');

if(file_exists(BOOTSTRAP)){
	require(BOOTSTRAP);
}else{
	echo 'Unable to load bootstrap file ' . BOOTSTRAP;
	if(function_exists("http_response_code")){
		http_response_code(500);
		exit;
	}
}