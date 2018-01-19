<?php
namespace App\Exceptions\Contract;

/**
* Describes an exception's blueprint.
*/

interface ExceptionContract
{

	/**
	* ExceptionContract constructor.
	*
	* @param 	$arguments <Array>
	* @access 	public
	* @return 	void
	*/
	public function __construct(...$arguments);

	/**
	* Sets response code.
	*
	* @param 	$code <Integer>
	* @access 	public
	* @return 	void
	*/
	public function setCode(int $code);

	/**
	* Sets exception message.
	*
	* @param 	$message <String>
	* @access 	public
	* @return 	void
	*/
	public function setMessage(String $message);

	/**
	* Sets exception view.
	*
	* @param 	$view <String>
	* @access 	public
	* @return 	void
	*/
	public function setView(String $view);

	/**
	* Returns response code.
	*
	* @access 	public
	* @return 	Integer
	*/
	public function getExceptionCode() : int;

	/**
	* Returns exception message.
	*
	* @access 	public
	* @return 	String
	*/
	public function getExceptionMessage() : String;

	/**
	* Returns exception view.
	*
	* @access 	public
	* @return 	String
	*/
	public function getView();

}