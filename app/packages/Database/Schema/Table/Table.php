<?php
namespace Package\Database\Schema;

use Schema;
use ArrayHelper;
use InvalidIndexTypeException;
use Package\Database\Factory;
use Package\Database\Schema\Column;
use Package\Database\Schema\Helper\Translator;
use Package\DependencyInjection\Injector\InjectorBridge;

class Table extends InjectorBridge {

	/**
	* @var 		$engine
	* @access 	public
	*/
	public 		$engine;

	/**
	* @var 		$name
	* @access 	public
	*/
	public 		$name;

	/**
	* @var 		$columns
	* @access 	public
	*/
	public 		$columns;

	/**
	* @var 		$index
	* @access 	private
	*/
	private 	$index=null;

	/**
	* @param 	$name\null
	* @param 	$columns <Array>
	* @param 	$engine <String>
	* @access 	public
	*/
	public function __construct($name=null, array $columns=[], $engine='InnoDB') {
		$this->name   = (String) $name;
		$this->engine = (String) $engine;
		$this->columns= (Array)  $columns;
	}

	/**
	* Creates a new table.
	* When using this method to create a table, note that the translator object method named [ct],
	* will not work for sqlite as it will throw an exception.
	*
	* @param 	$name <String>
	* @param 	$columns <Array>
	* @param 	$engine <String>
	* @throws 	DatabaseException
	* @access 	public
	* @return 	void
	*/
	public function create($name=null, array $columns=[], $engine=null) {
		if (null !== $name) {
			$this->name = $name;
		}
		if (!empty($columns)) {
			$this->columns = $columns;
		}
		if (null !== $engine) {
			$this->engine = $engine;
		}
		
		// Generating database query string using the translator object.
		$queryString = Translator::translate('ct', [$this->name, implode(',', $this->columns), $this->engine]);
		return $this->getFactory()->query($queryString);
	}

	/**
	* Drops a database table.
	*
	* @access 	public
	* @return 	void
	*/
	public function delete() {
		$queryString = Translator::translate('dt', [$this->name]);
		return $this->getFactory()->query($queryString);
	}

	/**
	* Drops the table only if it exists.
	*
	* @access 	public
	* @return 	void
	*/
	public function deleteIfFound() {
		if ($this->exists()) {
			$this->delete();
		}
	}

	/**
	* Checks if the table exists.
	*
	* @access 	public
	* @return 	Boolean
	*/
	public function exists() {
		$response = false;
		$connection = $this->getFactory();
		$connection->showTable($this->name);

		if (1 == $connection->numRows()) {
			$response = true;
		}
		return $response;
	}

	/**
	* Renames the table.
	*
	* @param 	$newName <String>
	* @access 	public
	* @return 	void
	*/
	public function rename($newName) {
		$queryString = Translator::translate('rt', [$this->name, $newName]);
		return $this->getFactory()->query($queryString);
	}

	/**
	* Checks if the table has a specific column.
	*
	* @param 	$column <String>
	* @access 	public
	*/
	public function hasColumn($column='') {
		$response = false;
		$query = $this->getFactory()->showColumn($this->name, $column)->numRows();
		if (1 == $query) {
			$response = true;
		}
		return $response;
	}

	/**
	* Adds a column to the table.
	*
	* @param 	$column <Mixed>
	* @access 	public
	* @return 	void
	*/
	public function addColumn($column='') {
		if ($column instanceof Column) {
			$preparedColumn = Column::getStaticField();
		}else{
			if (is_array($column)) {
				$preparedColumn = implode(',', $column);
			}else{
				$preparedColumn = $column;
			}
		}

		$queryString = Translator::translate('ac', [$this->name, $preparedColumn]);

		// Columns are being checked if they exist only when setting columns using Column object.
		if ($column instanceof Column && $this->hasColumn($column->field)) {
			return;
		}

		return $this->getFactory()->query($queryString);
	}

	/**
	* Creates a basic index on the table.
	*
	* @param 	$indexName <String>
	* @param 	$columns <Array>
	* @access 	public
	*/
	public function addIndex($indexName, array $columns=[]) {
		$queryString = Translator::translate('ai', [$indexName, $this->name, implode(',', $columns)]);
		return $this->getFactory()->query($queryString);
	}

	/**
	* Returns all indexes on a table.
	*
	* @param 	$array <Boolean>
	* @access 	public
	* @return 	Mixed
	*/
	public function getAllIndex($array=false) {
		$queryString = Translator::translate('gi', [$this->name]);
		$result = $this->getFactory()->query($queryString);
		if (boolval($array) === true) {
			return $result->get()->all();
		}
		return $result->getArray();
	}

	/**
	* Checks if the table has a specific index.
	*
	* @param 	$index <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function hasIndex($index='') {
		$response = false;
		$index = "'".$index."'";
		$queryString = Translator::translate('hix', [$this->name, $index]);
		$result = $this->getFactory()->query($queryString);
		if (1 == $result->numRows()) {
			$response = true;
		}
		
		return $response;
	}

	/**
	* Represents an index on the table.
	*
	* @param 	$index <String>
	* @access 	public
	* @return 	Object
	*/
	public function index($index='') {
		if ('' == $index) {
			return;
		}
		$this->index = $index;
		return $this;
	}

	/**
	* Adds an index to the table. Index type can be specified using the @param 	$type.
	*
	* @param 	$type <String> Accepts four index types. INDEX, PRIMARY KEY, UNIQUE AND FULLTEXT
	* @access 	public
	* @return 	void
	*/
	public function add($type='', array $columns=[]) {
		if ('' == $type || null === $this->index) {
			throw new InvalidIndexTypeException('Unable to create index on null.');
		}

		switch ($type) {
			case 'unique':
				$queryString = Translator::translate('aui', [$this->index, $this->name, implode(',', $columns)]);
				break;
			case 'primary':
				$queryString = Translator::translate('pki', [$this->name, $this->index]);
				break;
			case 'fulltext':
				$queryString = Translator::translate('fti', [$this->index, $this->name, implode(',', $columns)]);
				break;
			case 'key':
				return $this->addIndex($this->index, $columns);
				break;
		}
		return $this->getFactory()->query($queryString);
	}

	/**
	* Drops an index from the table.
	*
	* @param 	$index <String>
	* @access 	public
	* @return 	void
	*/
	public function dropIndex($index='') {
		$queryString = Translator::translate('di', [$this->name, $index]);
		return $this->getFactory()->query($queryString);
	}

	/**
	* Drops an index only if it exists.
	*
	* @param 	$index <String>
	* @access 	public
	* @return 	void
	*/
	public function dropIndexIfExists($index='') {
		if ($this->hasIndex($index)) {
			return $this->dropIndex($index);
		}
	}

	/**
	* Drops a column from the table.
	*
	* @param 	$column <String>
	* @access 	public
	* @return 	void
	*/
	public function dropColumn($column='') {
		$queryString = Translator::translate('dc', [$this->name, $column]);
		return $this->getFactory()->query($queryString);
	}

	/**
	* Drops a column only if it exists.
	*
	* @param 	$column <String>
	* @access 	public
	* @return 	void
	*/
	public function dropColumnIfExists($column='') {
		if ($this->hasColumn($column)) {
			return $this->dropColumn($column);
		}
	}

	/**
	* Drops a foreign key.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	void
	*/
	public function dropForeignKey($key='') {
		$queryString = Translator::translate('dfk', [$this->name, $key]);
		return $this->getFactory()->query($queryString);
	}

	/**
	* Returns an instance of Database\Schema\SchemaDataType
	*
	* @access 	public
	* @return 	Object
	*/
	public function column() {
		return new Column;
	}

	/**
	* Returns an instace of \Database\Factory.
	*
	* @access 	protected
	* @return 	Object
	*/
	protected function getFactory() {
		return new Factory();
	}

	/**
	* Returns an instance of Schema
	* @access 	private
	*/
	private function getSchemaInstance() {
		return new Schema();
	}

	/**
	* @inheritDoc
	* @param 	$column
	* @access 	private
	*/
	private function removeInvalidKeyword($column=null) {
		return $this->getSchemaInstance()->removeInvalidKeyword($column);
	}

}