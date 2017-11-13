<?php
namespace Package\FileSystem\File;

use StringHelper;
use BadWriterDataException;
use Package\FileSystem\File\FileManager;

class Writer
{

	/**
	* @var 		$file
	* @access 	private
	*/
	private 	$file;

	/**
	* @var 		$maxStringLength
	* @access 	private
	*/
	private static $maxStringLength=null;

	/**
	* @var 		$minStringLength
	* @access 	private
	*/
	private static $minStringLength=null;

	/**
	* @var 		$roNewLine
	* @access 	private
	*/
	private static $toNewLine=null;

	/**
	* Constructor
	*
	* @param 	$file
	* @access 	public
	*/
	public function __construct(FileManager $file)
	{
		$this->file = $file->getFile();
	}

	/**
	* Sets the maximum length of characters expected from a string.
	*
	* @param 	$length <Integer>
	* @access 	public
	* @return 	void
	*/
	public static function setMaximumLength($length=0)
	{
		return (Integer) Writer::$maxStringLength = $length;
	}

	/**
	* Sets the minimum length of characters expected from a string.
	*
	* @param 	$length <Integer>
	* @access 	public
	* @return 	void
	*/
	public static function setMinimumLength($length=0)
	{
		return (Integer) Writer::$minStringLength = $length;
	}

	/**
	* Runs all validations on length of data that will be saved.
	*
	* @param 	$data <String>
	* @access 	private
	* @throws 	RuntimeException
	* @return 	void.
	*/
	private static function validateDataLength($data) {
		if (null !== Writer::$minStringLength && Writer::$minStringLength > 0 && ctype_digit(Writer::$minStringLength)) {
			if (strlen($data) < Writer::$minStringLength) {
				throw new RuntimeException('Unable to write data into file. Data length is lower than required length.');
			}
		}

		if (null !== Writer::$maxStringLength && Writer::$maxStringLength > 0 && is_integer((Integer) Writer::$maxStringLength)) {
			if (strlen($data) > Writer::$maxStringLength) {
				throw new RuntimeException('Unable to write data into file. Data length is higher than required length.');
			}
		}

	}

	/**
	* Sets a flag that tells the file writer to append data to a new line.
	*
	* @access 	public
	* @return 	Boolean
	*/
	public static function toNewLine()
	{
		return (Boolean) Writer::$toNewLine = true;
	}

	/**
	* Writes data into a file.
	* The new line flag is not added at the beginning of the data because if the file is empty before
	* writing into it, it omits the first line of the file and makes it empty.
	*
	* @param 	$data <String>
	* @access 	public
	* @return 	void
	*/
	public function write($data='')
	{
		Writer::validateDataLength($data);

		if (true == boolval(Writer::$toNewLine)) {
			$data=$data."\n";
		}

		$filePointer = fopen($this->file, 'a');
		fwrite($filePointer, $data);
		fclose($filePointer);
	}

}