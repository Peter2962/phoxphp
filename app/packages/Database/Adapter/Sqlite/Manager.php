<?php
namespace Package\Database\Adapter\Sqlite;

use RuntimeException;
use Package\FileSystem\File\FileManager;

class Manager {

	/**
	* @var 		$database
	* @access 	protected
	*/
	protected 	$database;

	public function __construct($database='') {
		$this->database = $database;
	}

	/**
	* Creates a new sqlite database in public/storage/database directory.
	* @param 	$database <String>
	* @access 	public
	* @return 	void
	*/
	public function create($database='') {
		if ('' !== $database) {
			$this->database = $database;
		}
		if (!ctype_alpha($this->database)) {
			throw new RuntimeException(sprintf("Invalid database name. String expected, %s given", gettype($this->database)));
		}

		$file = new File($this->store($this->format($this->database)));
		if (!$this->exists()) {
			$file->create();
		}
	}

	/**
	* Checks if an sqlite database exists in public/storage/directory.
	* @param 	$database <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function exists($database='') {
		if ('' !== $database) {
			$this->database = $database;
		}

		$response = false;
		if (file_exists($this->store($this->format($this->database)))) {
			$response = true;
		}
		return $response;
	}

	/**
	* Deletes a sqlite database file in public/storage/database directory.
	* @param 	$database <String>
	* @access 	public
	* @return 	void
	*/
	public function delete($database='') {
		if ('' !== $database) {
			$this->database = $database;
		}
		$file = new File($this->store($this->format($this->database)));
		if ($this->exists()) {
			$file->delete();
		}
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	String
	*/
	private function format($string='') {
		return $string.'.db';
	}

	/**
	* @param 	$database <String>
	* @access 	private
	* @return 	String
	*/
	private function store($database='') {
		return config()->get('database', 'local_storage').$database;
	}

}