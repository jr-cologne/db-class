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
 * @copyright 2018 JR Cologne
 * @license https://github.com/jr-cologne/db-class/blob/master/LICENSE MIT
 * @version v2.3.0
 * @link https://github.com/jr-cologne/db-class GitHub Repository
 * @link https://packagist.org/packages/jr-cologne/db-class Packagist site
 *
 * ________________________________________________________________________________
 *
 * DatabaseTestCase.php
 *
 * The phpunit basic test case for database testing at db-class
 *
 */

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
