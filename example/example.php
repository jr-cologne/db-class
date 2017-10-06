<?php

require_once('../vendor/autoload.php');

use JRCologne\Utils\Database\DB;
use JRCologne\Utils\Database\QueryBuilder;

$db = new DB(new QueryBuilder);

if (!$db->connect('mysql:host=localhost;dbname=db-class-example;charset=utf8', 'root', 'root')) {
  echo 'Connection to database failed!<br>';

  $e = $db->getPDOException();

  if ($e) {
    echo '<pre>', print_r($e), '</pre>';
  }
} else {
  echo 'Successfully connected to database!<br>';

  for ($i = 0; $i < 3; $i++) {
    $data[] = [
      'username' => 'test' . $i,
      'password' => 'test' . $i,
    ];
  }

  $inserted = $db->table('users')->multi_insert('username, password', $data);

  if ($inserted) {
    echo 'Data has successfully been inserted!<br>';
  } else if ($inserted === 0) {
    echo 'Ops, some data could not be inserted!<br>';

    $e = $db->getPDOException();

    if ($e) {
      echo '<pre>', print_r($e), '</pre>';
    }
  } else {
    echo 'Inserting of data is failed!<br>';

    $e = $db->getPDOException();

    if ($e) {
      echo '<pre>', print_r($e), '</pre>';
    }
  }

  $data = $db->table('users')->select('*')->retrieve();
  
  if ($data === false) {
    echo 'Ops, something went wrong retrieving the data from the database!<br>';

    $e = $db->getPDOException();

    if ($e) {
      echo '<pre>', print_r($e), '</pre>';
    }
  } else if (empty($data)) {
    echo 'It looks like there is no data in the database!<br>';
  } else {
    echo 'Successfully retrieved the data from the database!<br>';

    echo '<pre>', print_r($data, true), '</pre>';
  }

  if (
    $db->table('users')->update(
      [
        'username' => 'test123',
        'password' => 'test123',
      ],
      [
        'username' => 'test1',
        'password' => 'test1',
      ]
    )
  ) {
    echo 'Data has successfully been updated!<br>';
  } else {
    echo 'Updating data failed!<br>';

    $e = $db->getPDOException();

    if ($e) {
      echo '<pre>', print_r($e), '</pre>';
    }
  }

  $data = $db->table('users')->select('*', [
    'username' => 'test123',
    'password' => 'test123',
  ])->retrieve('first');

  if ($data === false) {
    echo 'Ops, something went wrong retrieving the data from the database!<br>';

    $e = $db->getPDOException();

    if ($e) {
      echo '<pre>', print_r($e), '</pre>';
    }
  } else if (empty($data)) {
    echo 'It looks like there is no data in the database!<br>';
  } else {
    echo 'Successfully retrieved the data from the database!<br>';

    echo '<pre>', print_r($data, true), '</pre>';
  }

  if ($db->table('users')->delete()) {
    echo 'Data has successfully been deleted!<br>';
  } else {
    echo 'Deleting data failed!<br>';

    $e = $db->getPDOException();

    if ($e) {
      echo '<pre>', print_r($e), '</pre>';
    }
  }
}
