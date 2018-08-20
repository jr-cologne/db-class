<?php

namespace JRCologne\Utils\Database\Tests;

use PHPUnit\{
  Framework\TestCase,
  DbUnit\TestCaseTrait,
  DbUnit\DataSet\YamlDataSet as DataSet
};

abstract class DatabaseTestCase extends TestCase {

  use TestCaseTrait;

  static protected $pdo = null;

  protected $connection = null;

  final protected function getConnection() {
    if (!$this->connection) {
      if (!self::$pdo) {
        self::$pdo = new \PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
      }

      $this->connection = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
    }

    return $this->connection;
  }

  final protected function getDataSet() {
    return new DataSet(__DIR__ . '/db-class-testing-dataset.yml');
  }

}
