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
 * @version v2.1.1
 * @link https://github.com/jr-cologne/db-class GitHub Repository
 * @link https://packagist.org/packages/jr-cologne/db-class Packagist site
 *
 * ________________________________________________________________________________
 *
 * QueryBuilder.php
 *
 * The query builder class which is creating all queries for this database class.
 * 
 */

namespace JRCologne\Utils\Database;

class QueryBuilder {

  /**
   * The current table which will be used for the query.
   * 
   * @var string $table
   */
  protected $table;

  /**
   * The mode or type of query.
   *
   * Currently supported modes:
   * - select
   * - insert
   * - update
   * - delete
   * 
   * @var string $mode
   */
  protected $mode;

  /**
   * The string of columns for the query.
   * 
   * @var string $columns
   */
  protected $columns;

  /**
   * The string of the where clause for the query.
   * 
   * @var string $where
   */
  protected $where;

  /**
   * The string of values for the insert query.
   * 
   * @var string $values
   */
  protected $values;

  /**
   * The string of data for the update query.
   * 
   * @var string $data
   */
  protected $data;

  /**
   * Sets the table to be used in the query.
   * 
   * @param string $table
   */
  public function setTable(string $table) {
    $this->table = $table;
  }

  /**
   * Resets the properties for the query.
   */
  public function resetProperties() {
    $this->mode = $this->columns = $this->where = $this->values = $this->data = null;
  }

  /**
   * Sets the mode of the query.
   * 
   * @param string $mode
   */
  public function setMode(string $mode) {
    $this->mode = $mode;
  }

  /**
   * Sets the columns for the query.
   * 
   * @param mixed $columns
   */
  public function setColumns($columns) {
    $this->columns = $this->formatColumns($columns);
  }

  /**
   * Sets the where clause for the query.
   * 
   * @param array $where
   */
  public function setWhere(array $where) {
    $this->where = $this->formatWhere($where);
  }

  /**
   * Sets the values for the insert query.
   * 
   * @param mixed $values
   */
  public function setValues($values) {
    $this->values = $this->formatValues($values);
  }

  /**
   * Sets the data for the update query.
   * 
   * @param array $data
   */
  public function setData(array $data) {
    $this->data = $this->formatData($data);
  }

  /**
   * Gets the build query from the method QueryBuilder::build().
   * 
   * @return string
   */
  public function getQuery() {
    return $this->build();
  }

  /**
   * Formats the columns for the query.
   * 
   * @param  mixed $columns
   * @return string
   */
  protected function formatColumns($columns) {
    if (is_string($columns)) {
      if ($columns == '*') {
        return $columns;
      }

      $columns = explode(',', $columns);

      $columns = array_map(function($column) {
        return '`' . trim($column) . '`';
      }, $columns);

      return implode(', ', $columns);
    } else if (is_array($columns)) {
      return '`' . implode('`, `', $columns) . '`';
    }
  }

  /**
   * Formats the where clause for the query.
   * 
   * @param  array $where
   * @return string
   */
  protected function formatWhere(array $where) {
    $where_clause = '';

    foreach ($where as $column => $value) {
      $where_clause .= "`{$column}` = :where_{$column}";

      if (next($where) !== false) {
        $where_clause .= ' && ';
      }
    }

    return $where_clause;
  }

  /**
   * Formats the values for the insert query.
   * 
   * @param  mixed $values
   * @return string
   */
  protected function formatValues($values) {
    if (is_array($values)) {
      return ':' . implode(', :', $values);
    } else if (is_string($values)) {
      $values = explode(',', $values);

      $values = array_map(function($value) {
        return ':' . trim($value);
      }, $values);

      return implode(', ', $values);
    }
  }

  /**
   * Formats the data for the update query.
   * 
   * @param  array $data
   * @return string
   */
  protected function formatData(array $data) {
    $data_string = '';

    foreach ($data as $column => $value) {
      $data_string .= "`{$column}` = :{$column}";

      if (next($data) !== false) {
        $data_string .= ', ';
      }
    }

    return $data_string;
  }

  /**
   * Builds the query.
   * 
   * @return string
   */
  protected function build() {
    $table = $this->table;
    $mode = $this->mode;
    $columns = $this->columns;
    $where = $this->where;
    $values = $this->values;
    $data = $this->data;

    $query = '';

    switch ($mode) {
      case 'select':
        if (!empty($where)) {
          $query = "SELECT {$columns} FROM `{$table}` WHERE {$where}";
        } else {
          $query = "SELECT {$columns} FROM `{$table}`";
        }
        break;
      case 'insert':
        $query = "INSERT INTO `{$table}` ({$columns}) VALUES ({$values})";
        break;
      case 'update':
        if (!empty($where)) {
          $query = "UPDATE `{$table}` SET {$data} WHERE {$where}";
        } else {
          $query = "UPDATE `{$table}` SET {$data}";
        }
        break;
      case 'delete':
        if (!empty($where)) {
          $query = "DELETE FROM `{$table}` WHERE {$where}";
        } else {
          $query = "DELETE FROM `{$table}`";
        }
        break;
    }

    return $query;
  }
  
}
