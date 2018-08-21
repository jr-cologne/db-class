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
 * @version v2.2.0
 * @link https://github.com/jr-cologne/db-class GitHub Repository
 * @link https://packagist.org/packages/jr-cologne/db-class Packagist site
 *
 * ________________________________________________________________________________
 *
 * DBTest.php
 *
 * The phpunit test class for the DB class (DB.php)
 *
 */

namespace JRCologne\Utils\Database\Tests;

use \TypeError;
use \PDOException;

use JRCologne\Utils\Database\DB;
use JRCologne\Utils\Database\QueryBuilder;
use JRCologne\Utils\Database\Exceptions\UnsupportedKeywordException;

class DBTest extends DatabaseTestCase {

  public function test_constructor_returns_db_instance() {
    $this->assertInstanceOf(DB::class, new DB(new QueryBuilder));
  }

  public function test_constructor_without_query_builder_throws_exception() {
    $this->expectException(TypeError::class);

    new DB;
  }

  public function test_connect_method_returns_true_on_success() {
    $this->assertTrue(
      (new DB(new QueryBuilder))
        ->connect($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'])
    );
  }

  public function test_connect_method_returns_false_on_failure() {
    $this->assertFalse(
      (new DB(new QueryBuilder))
        ->connect($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], 'wrong')
    );
  }

  public function test_connected_method_returns_bool() {
    $this->assertInternalType('bool',
      (new DB(new QueryBuilder))
        ->connected()
    );
  }

  public function test_connected_method_returns_correct_connection_status() {
    $db1 = new DB(new QueryBuilder);
    $db2 = new DB(new QueryBuilder);

    $db1->connect($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
    $db2->connect($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], 'wrong');

    $this->assertNotEquals($db1->connected(), $db2->connected());
  }

  public function test_table_method_returns_db_instance() {
    $this->assertInstanceOf(DB::class,
      $this->getDBConnection()
        ->table('users')
    );
  }

  public function test_select_method_returns_db_instance() {
    $this->assertInstanceOf(DB::class,
      $this->getDBConnection()
        ->table('users')
        ->select('*')
    );
  }

  public function test_retrieve_method_returns_all_data() {
    $this->assertCount(2,
      $this->getDBConnection()
        ->table('users')
        ->select('*')
        ->retrieve()
    );
  }

  public function test_retrieve_method_returns_first_record() {
    $this->assertArrayHasKey('username',
      $this->getDBConnection()
        ->table('users')
        ->select('*')
        ->retrieve('first')
    );
  }

  public function test_retrieve_method_returns_data_based_on_where_clause() {
    $this->assertEquals('John',
      $this->getDBConnection()
        ->table('users')
        ->select('username', [ 'username' => 'John' ])
        ->retrieve('first')['username']
    );
  }

  public function test_retrieve_method_returns_data_based_on_fetch_mode() {
    $this->assertInternalType('object',
      $this->getDBConnection()
        ->table('users')
        ->select('*', [], \PDO::FETCH_OBJ)
        ->retrieve('first')
    );
  }

  public function test_retrieve_method_returns_false_if_query_fails() {
    $this->assertFalse(
      $this->getDBConnection()
        ->table('wrong')
        ->select('*')
        ->retrieve()
    );
  }

  public function test_retrieve_method_returns_empty_array_if_no_data_was_found() {
    $this->assertEquals([],
      $this->getDBConnection()
        ->table('users')
        ->select('*', [ 'username' => 'wrong' ])
        ->retrieve()
    );
  }

  public function test_retrieve_method_returns_null_if_first_record_does_not_exist() {
    $this->assertNull(
      $this->getDBConnection()
        ->table('users')
        ->select('*', [ 'username' => 'wrong' ])
        ->retrieve('first')
    );
  }

  public function test_retrieve_method_with_wrong_keyword_throws_exception() {
    $this->expectException(UnsupportedKeywordException::class);

    $this->getDBConnection()
      ->table('users')
      ->select('*')
      ->retrieve('wrong');
  }

  public function test_insert_method_returns_true_on_success() {
    $this->assertTrue(
      $this->getDBConnection()
        ->table('users')
        ->insert([
          'username' => 'Bob',
          'password' => 'ilovecats123'
        ])
    );
  }

  public function test_insert_method_returns_false_on_failure() {
    $this->assertFalse(
      $this->getDBConnection()
        ->table('users')
        ->insert([
          'wrong' => 'Bob',
          'password' => 'ilovecats123'
        ])
    );
  }

  public function test_insert_method_inserts_data_into_database() {
    $this->getDBConnection()
      ->table('users')
      ->insert([
        'username' => 'Bob',
        'password' => 'ilovecats123'
      ]);

    $this->assertEquals('Bob',
      $this->getDBConnection()
        ->table('users')
        ->select('username', [ 'username' => 'Bob' ])
        ->retrieve('first')['username']
    );
  }

  public function test_multi_insert_method_returns_true_on_success() {
    $this->assertTrue(
      $this->getDBConnection()
        ->table('users')
        ->multi_insert('username, password', [
          [
            'username' => 'Bob',
            'password' => 'ilovecats123'
          ],
          [
            'username' => 'Catherine',
            'password' => 'ilovecats123'
          ]
        ])
    );
  }

  public function test_multi_insert_method_returns_false_on_failure() {
    $this->assertFalse(
      $this->getDBConnection()
        ->table('wrong')
        ->multi_insert('username, password', [
          [
            'username' => 'Bob',
            'password' => 'ilovecats123'
          ],
          [
            'username' => 'Catherine',
            'password' => 'ilovecats123'
          ]
        ])
    );
  }

  public function test_multi_insert_method_returns_zero_if_at_least_one_record_was_inserted() {
    $this->assertEquals(0,
      $this->getDBConnection()
        ->table('users')
        ->multi_insert('username, password', [
          [
            'username' => 'Bob',
            'password' => 'ilovecats123'
          ],
          [
            'wrong' => 'Catherine',
            'password' => 'ilovecats123'
          ]
        ])
    );
  }

  public function test_multi_insert_method_inserts_all_data_into_database() {
    $this->getDBConnection()
      ->table('users')
      ->multi_insert('username, password', [
        [
          'username' => 'Bob',
          'password' => 'ilovecats123'
        ],
        [
          'username' => 'Catherine',
          'password' => 'ilovecats123'
        ]
      ]);

    $this->assertCount(4,
      $this->getDBConnection()
        ->table('users')
        ->select('*')
        ->retrieve()
    );
  }

  public function test_multi_insert_method_inserts_at_least_one_record_into_database() {
    $this->getDBConnection()
      ->table('users')
      ->multi_insert('username, password', [
        [
          'username' => 'Bob',
          'password' => 'ilovecats123'
        ],
        [
          'wrong' => 'Catherine',
          'password' => 'ilovecats123'
        ]
      ]);

    $this->assertCount(3,
      $this->getDBConnection()
        ->table('users')
        ->select('*')
        ->retrieve()
    );
  }

  public function test_update_method_returns_true_on_success() {
    $this->assertTrue(
      $this->getDBConnection()
        ->table('users')
        ->update([
          'username' => 'Johnny'
        ], [
          'username' => 'John'
        ])
    );
  }

  public function test_update_method_returns_false_on_failure() {
    $this->assertFalse(
      $this->getDBConnection()
        ->table('users')
        ->update([
          'wrong' => 'Johnny'
        ], [
          'username' => 'John'
        ])
    );
  }

  public function test_update_method_updates_data_in_database() {
    $this->getDBConnection()
      ->table('users')
      ->update([
        'username' => 'Johnny'
      ], [
        'username' => 'John'
      ]);

    $this->assertEquals('Johnny',
      $this->getDBConnection()
        ->table('users')
        ->select('username', [ 'username' => 'Johnny' ])
        ->retrieve('first')['username'] ?? null
    );
  }

  public function test_update_method_without_where_clause_updates_all_data_in_database() {
    $this->getDBConnection()
      ->table('users')
      ->update([
        'password' => 'ilovecats'
      ]);

    $this->assertCount(2,
      $this->getDBConnection()
        ->table('users')
        ->select('*', [ 'password' => 'ilovecats' ])
        ->retrieve()
    );
  }

  public function test_delete_method_returns_true_on_success() {
    $this->assertTrue(
      $this->getDBConnection()
        ->table('users')
        ->delete([
          'username' => 'John'
        ])
    );
  }

  public function test_delete_method_returns_false_on_failure() {
    $this->assertFalse(
      $this->getDBConnection()
        ->table('users')
        ->delete([
          'wrong' => 'John'
        ])
    );
  }

  public function test_delete_method_deletes_data_in_database() {
    $this->getDBConnection()
      ->table('users')
      ->delete([
        'username' => 'John'
      ]);

    $this->assertCount(1,
      $this->getDBConnection()
        ->table('users')
        ->select('*')
        ->retrieve()
    );
  }

  public function test_delete_method_without_where_clause_deletes_all_data_in_database() {
    $this->getDBConnection()
      ->table('users')
      ->delete();

    $this->assertCount(0,
      $this->getDBConnection()
        ->table('users')
        ->select('*')
        ->retrieve()
    );
  }

  public function test_get_pdo_exception_method_returns_pdo_exception_instance_when_pdo_raised_error() {
    $db = $this->getDBConnection();

    $db
      ->table('wrong')
      ->select('*')
      ->retrieve();

    $this->assertInstanceOf(PDOException::class, $db->getPDOException());
  }

  public function test_get_pdo_exception_method_throws_exception_when_pdo_raised_no_error() {
    $this->expectException(TypeError::class);

    $db = $this->getDBConnection();

    $db
      ->table('users')
      ->select('*')
      ->retrieve();

    $this->assertNull($db->getPDOException());
  }

  protected function getDBConnection() {
    $db = new DB(new QueryBuilder);

    $db->connect($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);

    return $db;
  }

}
