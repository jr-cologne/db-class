# db-class
This project is a simple database class with php and pdo.

## Installation/Setup
If you want to use the database class for your own project, you can simply follow these instructions:

1. Download the ZIP file of this project
2. Unzip it and save the file `DB.php` to your own project directory.
3. If all requirements, like PHP, PDO and a database that fits to that setup, are fulfilled, you should now be ready to start!

## Basic Usage
If you have successfully "installed" everything, you can use the class like that:

### Require database class
```php
require_once('your_path/DB.php');
```

### Instantiate Class / Connect to Database
To be able to use the class and connect to the database, you have to instantiate it.

To do that, follow that format:

```php
new DB(string $dbname, string $user, string $password, string $db_type='mysql', string $host='localhost', int $pdo_err_mode=PDO::ERRMODE_EXCEPTION)
```

It's providing these options:

#### Database Name ('$dbname')
The name of your database you want to connect with. (required)

#### Database User ('$user')
The user of the database that should interact with it. (required)

#### Password of the Database User ('$password')
The password of the database user you are using to interact with the database. (required)

#### Database Type ('$db_type')
The type of the database you are using. (optional)

Default: `mysql`

#### Host ('$host')
The host of your database. (optional)

Default: `localhost`

#### PDO Error Mode ('$pdo_err_mode')
The [error mode of pdo](http://php.net/manual/en/pdo.error-handling.php) you want to use. (optional)

Default: `PDO::ERRMODE_EXCEPTION`

Simple example for instantiating the class:
```php
$db = new DB('db-class-example', 'root', '');
```

### Check Connection to Database
The method `connected()` gives you the ability to check if the connection to the database was established successfully.

The method requires no arguments at all, so you can just call it and then it will give you a return value of `true` or `false` based on the fact if the connection was established or not.

Example:
```php
if ($db->connected()) {
  echo 'Successfully connected!';
} else {
  echo 'Connection failed';
}
```

### Select/Get Data from Database
In order to get data from the database you can use the method `select()` in the following format:

```php
select(string $sql, $where=null, $fetch_mode=PDO::FETCH_ASSOC)
```

The following arguments exist:

#### SQL Query (`$sql`)
The sql to perform the query to the database. (required)

For example:

```sql
SELECT * FROM `users`
```

#### SQL Where Clause (`$where`)
The sql where clause to filter the data from the database and so on. (optional)

Default: `null`

Example:
```sql
WHERE `username` = 'Jack' 
```

#### PDO Fetch Mode (`$fetch_mode`)
The [pdo fetch mode](http://php.net/manual/en/pdostatement.fetch.php). Defines in which format the data is returned from the database. (optional)

Default: `PDO::FETCH_ASSOC`

Example:

```php
PDO::FETCH_NUM
```

An simple example for using everything together:

```php
$data = $db->select("SELECT * FROM `users`", "WHERE `username` = 'Jack'", PDO::FETCH_NUM);
```

### Configure Error Handling

If you want to, you can create your own error handling setup before you instantiate the class.

Important to know is that every method returns the array `$error` on failure with some basic information about the error that is occured.

The following options exist:

#### Environment (`$env`)
`production` or `development`/`dev`

Production: return simple error code and the related error message (default)

Development: return simple error code, the related error message and the [`PDOException Object`](http://php.net/manual/en/class.pdoexception.php)

#### Error Types / Error Messages ('$error_types')
An array of the error messages with the error code as the key.

Default:
```php
[
      0 => 'success',
      1 => 'Connection to database failed',
      2 => 'Selecting/Getting data from database failed',
      3 => 'Inserting data into database failed',
      4 => 'Deleting data from database failed',
      5 => 'Updating data in database failed',
]
```

**Attention**: Do not change the error codes/keys as long as you don't modify the class according to that! When you change the error code of an error and then just use the database class as normal, it will not work as expected!

Besides from that you can freely change the error messages to your own liking.

To change the config of the error handling, you must call the static method `initErrorHandler(array $error_types=[], string $env='production')`, which will basically set your specified configs, **before** you are instantiating the class.

Example:
```php
DB::initErrorHandler(
    [
      0 => 'success',
      1 => 'Sorry, the connection to the database is failed!',
      2 => 'Sorry, we are currently not able to receive data from the database!',
      3 => 'Sorry, we are currently not able to insert your data to the database!',
      4 => 'Sorry, we are currently not able to delete your data from the database!',
      5 => 'Sorry, we are currently not able to update your data in the database!',
    ]
);
```

Always make sure to pass in the whole array, e.g. not just error 2 and 5, because then only error 2 and 5 will exist.

In case you don't want to change the messages, but you would like to switch the environment, you have to pass in an empty array as the first argument like that:

```php
DB::initErrorHandler(
    [],
    'development'
);
```
