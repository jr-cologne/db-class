<?php

/**
 * A simple database class with PHP, PDO and a query builder.
 *
 * The class extends PDO for more control and in order to keep all features of PDO.
 *
 * PHP version >= 7.0
 *
 * LICENSE: MIT, see LICENSE file for more information
 *
 * @package jr-cologne/db-class
 * @author JR Cologne <kontakt@jr-cologne.de>
 * @copyright 2017 JR Cologne
 * @license https://github.com/jr-cologne/db-class/blob/master/LICENSE MIT
 * @version v2.0.0
 * @link https://github.com/jr-cologne/db-class GitHub Repository
 * @link https://packagist.org/packages/jr-cologne/db-class Packagist site
 *
 * ________________________________________________________________________________
 *
 * DB.php
 *
 * The main and only class you actively work with in order to interact with a database.
 * 
 */

namespace JRCologne\Utils\Database;

use JRCologne\Utils\Database\QueryBuilder;
use JRCologne\Utils\Database\Exceptions\DBException;

use \PDO;
use \PDOException;

class DB extends PDO {

  /**
   * The instance of the query builder.
   * 
   * @var QueryBuilder $query_builder
   */
  protected $query_builder;

  /**
   * The default options of this class for PDO, which are passed into PDO::__construct.
   * 
   * @var array $default_options
   */
  protected $default_options = [
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ];

  /**
   * The results of the query from the method DB::select().
   *
   * The type is based on PDO::FETCH_MODE.
   * Default: array
   * 
   * @var mixed $results
   */
  protected $results;

  /**
   * Indicator whether the query of the method DB::select() failed.
   *
   * Value:
   * true if query failed,
   * false if query was successful
   * 
   * @var boolean $query_failed
   */
  protected $query_failed = false;

  /**
   * An PDOException instance. Is set when PDO raises an error.
   * 
   * @var PDOException $pdo_exception
   */
  protected $pdo_exception;

  /**
   * Instantiates the class and sets DB::query_builder to the passed in QueryBuilder instance.
   * 
   * @param QueryBuilder $query_builder
   */
  public function __construct(QueryBuilder $query_builder) {
    $this->query_builder = $query_builder;
  }

  /**
   * Creates a connection to a specified database by PDO.
   * 
   * @param  string $dsn the data source name of the method PDO::__construct()
   * @param  string $username = null
   * @param  string $password = null
   * @param  array $options = []
   * @return boolean true on success, false on failure
   */
  public function connect(string $dsn, $username = null, $password = null, $options = []) {
    try {
      if (empty($options)) {
        parent::__construct($dsn, $username, $password, $this->default_options);
      } else {
        parent::__construct($dsn, $username, $password, $options);
      }
    } catch (PDOException $e) {
      $this->pdo_exception = $e;
      return false;
    }

    return true;
  }

  /**
   * Sets the table to use for the following query.
   * 
   * @param  string $table
   * @return DB
   */
  public function table(string $table) {
    $this->query_builder->setTable($table);
    return $this;
  }

  /**
   * Executes a SELECT query and fetches data from a database.
   *
   * @param  string $columns
   * @param  array $where = [] assoc array with format column => value
   * @param  int $fetch_mode = PDO::FETCH_ASSOC the wished pdo fetch mode
   * @return DB
   */
  public function select(string $columns, array $where = [], int $fetch_mode = PDO::FETCH_ASSOC) {
    $this->query_builder->resetProperties();
    $this->query_builder->setMode('select');
    $this->query_builder->setColumns($columns);

    if (!empty($where)) {
      $this->query_builder->setWhere($where);
      $where = $this->formatWhereData($where);
    }

    try {
      $stmt = parent::prepare($this->query_builder->getQuery());
      $stmt->execute($where);
      $this->results = $stmt->fetchAll($fetch_mode);
    } catch (PDOException $e) {
      $this->pdo_exception = $e;
      $this->query_failed = true;
    }

    return $this;
  }

  /**
   * Retrieves an particular amount of the data selected and fetched by the method DB::select().
   *
   * Keywords:
   * - 'all' retrieves the whole dataset
   * - 'first' retrieves the first record
   * 
   * @param  string $keyword = 'all' the keyword for the amount of data to retrieve
   * @return mixed based on method called by $keyword
   * @throws UnsupportedKeywordException is thrown if wrong $keyword is used 
   */
  public function retrieve(string $keyword = 'all') {
    if (method_exists($this, $keyword)) {
      return $this->$keyword();
    } else {
      throw new UnsupportedKeywordException("Unsupported Keyword for method DB::retrieve()", 1);
    }
  }

  /**
   * Returns the whole dataset given in DB::results.
   * 
   * @return mixed on success: array (default), based on PDO::FETCH_MODE, on failure: boolean false
   */
  protected function all() {
    if (!$this->query_failed) {
      return $this->results;
    }

    return false;
  }

  /**
   * Returns the first record given in DB::results.
   * 
   * @return mixed on success: array (default), based on PDO::FETCH_MODE, on failure: boolean false
   */
  protected function first() {
    if (!$this->query_failed) {
      return $this->results[0];
    }

    return false;
  }

  /**
   * Inserts data into a database.
   * 
   * @param  array $data assoc array of data to insert with the format column => value
   * @return boolean true on success, false on failure
   */
  public function insert(array $data) {
    $this->query_builder->resetProperties();
    $this->query_builder->setMode('insert');

    $columns = array_keys($data);

    $this->query_builder->setColumns($columns);
    $this->query_builder->setValues($columns);

    try {
      $stmt = parent::prepare($this->query_builder->getQuery());
      return $stmt->execute($data);
    } catch (PDOException $e) {
      $this->pdo_exception = $e;
      return false;
    }
  }

  /**
   * Inserts multiple rows of data into a database.
   * 
   * @param  string $columns
   * @param  array $data 2d array with assoc array of data (with the format column => value) for each row
   * @return mixed true on success, 0 if at least one record could be inserted, false on failure
   */
  public function multi_insert(string $columns, array $data) {
    $this->query_builder->resetProperties();
    $this->query_builder->setMode('insert');
    $this->query_builder->setColumns($columns);
    $this->query_builder->setValues($columns);

    try {
      $stmt = parent::prepare($this->query_builder->getQuery());

      $execution = [];

      foreach ($data as $value) {
        $execution[] = (int) $stmt->execute($value);
      }

      $data_amount = count($data);
      $successful_executions = array_sum($execution);

      if ($data_amount == $successful_executions) {
        return true;
      } else if ($data_amount != $successful_executions && $successful_executions >= 1) {
        return 0;
      } else {
        return false;
      }
    } catch (PDOException $e) {
      $this->pdo_exception = $e;
      return false;
    }
  }

  /**
   * Updates data of a database.
   *
   * Attention: If you do not provide a where clause,
   * all records of the table will be updated.
   * 
   * @param  array $data assoc array of new data with the format column => value
   * @param  array $where = [] assoc array with format column => value
   * @return boolean true on success, false on failure  
   */
  public function update(array $data, array $where = []) {
    $this->query_builder->resetProperties();
    $this->query_builder->setMode('update');

    $this->query_builder->setData($data);

    if (!empty($where)) {
      $this->query_builder->setWhere($where);
      $where = $this->formatWhereData($where);
    }

    try {
      $stmt = parent::prepare($this->query_builder->getQuery());
      return $stmt->execute(array_merge($data, $where));
    } catch (PDOException $e) {
      $this->pdo_exception = $e;
      return false;
    }
  }

  /**
   * Deletes data from a database.
   *
   * Attention: If you do not provide a where clause,
   * all records of the table will be deleted.
   * 
   * @param  array $where = [] assoc array with the format column => value
   * @return boolean true on success, false on failure
   */
  public function delete(array $where = []) {
    $this->query_builder->resetProperties();
    $this->query_builder->setMode('delete');

    if (!empty($where)) {
      $this->query_builder->setWhere($where);
      $where = $this->formatWhereData($where);
    }

    try {
      $stmt = parent::prepare($this->query_builder->getQuery());
      return $stmt->execute($where);
    } catch (PDOException $e) {
      $this->pdo_exception = $e;
      return false;
    }
  }

  /**
   * Returns the PDOException set when PDO raised an error.
   * 
   * @return PDOException
   */
  public function getPDOException() {
    return $this->pdo_exception;
  }

  /**
   * Formats the data for the where clause, which is passed into the method PDOStatement::execute().
   *
   * This is done in order to prevent issues/conflicts
   * when a particular column should be updated or deleted and is also part of the where clause.
   *
   * For example, the following would not work correctly without the help of this method:
   *
   * $db->update(
   *   [ 'username' => 'new_username' ],
   *   [ 'username' => 'old_username' ]
   * );
   *
   * For consistency, the method is used in every sort of query method with a potential where clause.
   * 
   * @param  array $where assoc array with the format column => value
   * @return array
   */
  protected function formatWhereData(array $where) {
    foreach ($where as $column => $value) {
      $where_data['where_' . $column] = $value;
    }

    return $where_data;
  }

}
