<?php

namespace JRCologne\Utils\Database;

use JRCologne\Utils\Database\QueryBuilder;
use JRCologne\Utils\Database\Exceptions\DBException;

use \PDO;
use \PDOException;

class DB extends PDO {
  protected $query_builder;

  protected $default_options = [
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ];

  protected $table;
  protected $results;
  protected $query_failed = false;

  public function __construct(QueryBuilder $query_builder) {
    $this->query_builder = $query_builder;
  }

  public function connect($dsn, $username = null, $password = null, $options = []) {
    try {
      if (empty($options)) {
        parent::__construct($dsn, $username, $password, $this->default_options);
      } else {
        parent::__construct($dsn, $username, $password, $options);
      }
    } catch (PDOException $e) {
      return false;
    }

    return true;
  }

  public function table(string $table) {
    $this->table = $table;
    $this->query_builder->setTable($table);
    return $this;
  }

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
      $this->query_failed = true;
    }

    return $this;
  }

  public function retrieve(string $keyword = 'all') {
    if (method_exists($this, $keyword)) {
      return $this->$keyword();
    } else {
      throw new DBException("Unsupported Keyword for method DB->retrieve()", 1);
    }
  }

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
      return false;
    }
  }

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
      return false;
    }
  }

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
      return false;
    }
  }

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
      return false;
    }
  }

  protected function all() {
    if (!$this->query_failed) {
      return $this->results;
    }

    return false;
  }

  protected function first() {
    if (!$this->query_failed) {
      return $this->results[0];
    }

    return false;
  }

  protected function formatWhereData(array $where) {
    foreach ($where as $column => $value) {
      $where_data['where_' . $column] = $value;
    }

    return $where_data;
  }
}