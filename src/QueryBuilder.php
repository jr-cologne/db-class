<?php

namespace JRCologne\Utils\Database;

class QueryBuilder {
  protected $table;
  protected $mode;
  protected $columns;
  protected $where;
  protected $values;
  protected $data;

  public function setTable(string $table) {
    $this->table = $table;
  }

  public function resetProperties() {
    $this->mode = $this->columns = $this->where = $this->values = $this->data = null;
  }

  public function setMode(string $mode) {
    $this->mode = $mode;
  }

  public function setColumns($columns) {
    $this->columns = $this->formatColumns($columns);
  }

  public function setWhere(array $where) {
    $this->where = $this->formatWhere($where);
  }

  public function setValues($values) {
    $this->values = $this->formatValues($values);
  }

  public function setData(array $data) {
    $this->data = $this->formatData($data);
  }

  public function getQuery() {
    return $this->build();
  }

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

  protected function formatWhere(array $where) {
    $where_clause = '';

    foreach ($where as $column => $value) {
      $where_clause .= "`{$column}` = :where_{$column}";

      if (!empty(next($where))) {
        $where_clause .= ' && ';
      }
    }

    return $where_clause;
  }

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

  protected function formatData(array $data) {
    $data_string = '';

    foreach ($data as $column => $value) {
      $data_string .= "`{$column}` = :{$column}";

      if (!empty(next($data))) {
        $data_string .= ', ';
      }
    }

    return $data_string;
  }

  protected function build() {
    $table = $this->table;
    $mode = $this->mode;
    $columns = $this->columns;
    $where = $this->where;
    $values = $this->values;
    $data = $this->data;

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