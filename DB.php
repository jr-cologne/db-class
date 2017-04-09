<?php
  class DB {  
    public $pdo = null;
    protected $errors = [ 'code' => 0, 'msg' => null ];

    // connect to database
    public function __construct(string $dbname, string $user, string $password, string $db_type='mysql', string $host='localhost', int $pdo_err_mode=PDO::ERRMODE_EXCEPTION) {
      $errors = $this->errors;

      try {
        $pdo = new PDO($db_type . ':host=' . $host . ';dbname=' . $dbname, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, $pdo_err_mode);
      } catch (PDOException $e) {
        $errors = [ 'code' => 1, 'msg' => 'Connection to database failed' ];
        $pdo = null;
      }

      $this->errors = $errors;
      $this->pdo = $pdo;
    }

    // check for errors
    public function checkErrors() {
      $errors = $this->errors;

      return ( !empty($errors) && $errors['code'] != 0 && $errors['msg'] != null ) ? true : false;
    }

    // get all errors
    public function getErrors() {
      return $this->errors;
    }

    // check if connection to database is established
    public function connected() {
      return (!empty($this->pdo)) ? true : false; 
    }

    // select/get data from database
    public function select(string $sql, $where=null, $fetch_mode=PDO::FETCH_ASSOC) {
      $pdo = $this->pdo;

      try {
        $statement = $pdo->prepare($sql);

        if (!empty($where)) {
          $execution = $statement->execute($where);
        } else {
          $execution = $statement->execute();
        }

        $results = $statement->fetchAll($fetch_mode);
      } catch (PDOException $e) {
        $errors = [ 'code' => 2, 'msg' => 'Selecting/getting data from database failed' ];
        return $errors;
      }

      if ($execution === true && !empty($results)) {
        return $results;
      } else if ($execution === true && empty($results)) {
        return null;
      } else {
        return false;
      }
    }

    // insert data into database
    public function insert(string $sql, array $values) {
      $pdo = $this->pdo;

      try {
        $statement = $pdo->prepare($sql);
        $execution = $statement->execute($values);
      } catch (PDOException $e) {
        $errors = [ 'code' => 3, 'msg' => 'Inserting data into database failed' ];
        return $errors;
      }

      if ($execution) {
        return true;
      } else {
        return false;
      }
    }

    // delete data/rows from database
    public function delete(string $sql, $where=null) {
      $pdo = $this->pdo;

      try {
        $statement = $pdo->prepare($sql);

        if (!empty($where)) {
          $execution = $statement->execute($where);
        } else {
          $execution = $statement->execute();
        }
      } catch (PDOException $e) {
        $errors = [ 'code' => 4, 'msg' => 'Deleting data/rows from database failed' ];
        return $errors;
      }

      if ($execution) {
        return true;
      } else {
        return false;
      }
    }

    // update data/rows in database
    public function update(string $sql, $values) {
      $pdo = $this->pdo;

      try {
        $statement = $pdo->prepare($sql);

        $execution = $statement->execute($values);
      } catch (PDOException $e) {
        $errors = [ 'code' => 5, 'msg' => 'Updating data/rows in database failed' ];
        return $errors;
      }

      if ($execution) {
        return true;
      } else {
        return false;
      }
    }
  }
?>