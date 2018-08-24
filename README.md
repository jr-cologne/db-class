# db-class

[![Build Status](https://travis-ci.org/jr-cologne/db-class.svg?branch=master)](https://travis-ci.org/jr-cologne/db-class)

This project is a simple database class with PHP, PDO and a query builder.

The class extends PDO for more control and in order to keep all features of PDO.

## Requirements

- [PHP](http://php.net) (version 7.0 or higher)
- Database, which supports PDO (e.g. MySQL)

## Installation

If you want to use the database class for your own project, you have two options to install it:

### Using Composer (recommended)

Once you have installed [Composer](https://getcomposer.org/), execute this command:

```
composer require jr-cologne/db-class
```

Then you just have to include the autoloader:

```php
require_once 'vendor/autoload.php';
```

### Manual Installation

1. Download the ZIP file of this project
2. Unzip it and move everything to your own project directory.
3. Include all files of the database class into your project like that:

```php
require_once 'path/to/db-class/src/DB.php';
require_once 'path/to/db-class/src/QueryBuilder.php';
require_once 'path/to/db-class/src/Exceptions/UnsupportedKeywordException.php';
```

Now you should be ready to start!

## Basic Usage

### Namespace

Before instantiating the class, always make sure to use the right namespaces:

```php
use JRCologne\Utils\Database\DB;
use JRCologne\Utils\Database\QueryBuilder;
```

### Instantiating Class

To be able to use the class, you have to instantiate it.

Just do this:

```php
$db = new DB(new QueryBuilder);
```

### Connecting to Database

You can connect to a database with the help of the method `DB::connect()`.

An simple example:

```php
if ($db->connect('mysql:host=localhost;dbname=db-class-example;charset=utf8', 'root', 'root')) {
  echo 'Successfully connected to database';
} else {
  echo 'Connection failed';
}
```

### Checking Connection to Database

You can also check the connection to the database by the method `DB::connected()` after connecting.

Example:

```php
if ($db->connected()) {
  echo 'Successfully connected to database';
} else {
  echo 'Connection failed';
}
```

### Retrieving Data from Database

In order to retrieve data from a database, you need to walk through the following three steps:

1. Choose a table with the method `DB::table()`.
2. Select the data you want to retrieve.
3. Retrieve the selected data.

Fortunately, this is super simple with the database class:

```php
$data = $db->table('users')->select('*')->retrieve();

if ($data === false) {
  echo 'Ops, something went wrong retrieving the data from the database!<br>';
} else if (empty($data)) {
  echo 'It looks like there is no data in the database!<br>';
} else {
  echo 'Successfully retrieved the data from the database!<br>';

  echo '<pre>', print_r($data, true), '</pre>';
}
```

It will basically retrieve all records from the selected table.

### Inserting Data into Database

If you want to insert data into a database, you have two methods which you can use:

- `DB::insert()` (to insert one row of data)
- `DB::multi_insert()` (to insert multiple rows of data)

In this case, we are just going to insert one row.

The procedure is as follows:

1. Choose a table with the method `DB::table()`.
2. Insert the data with the method `DB::insert()`.

Example:

```php
$inserted = $db->table('users')->insert('username, password', [
  'username' => 'test',
  'password' => 'password'
]);

if ($inserted) {
  echo 'Data has successfully been inserted';
} else if ($inserted === 0) {
  echo 'Ops, some data could not be inserted';
} else {
  echo 'Inserting of data is failed';
}
```

### Updating Data from Database

In case you want to update data from a database, you can use the method `DB::update()`.

The following steps are required:

1. Choose a table with the method `DB::table()`.
2. Update the data with the method `DB::update()`.

Example:

```php
if (
  $db->table('users')->update(
    [
      'username' => 'test123',  // new data
      'password' => 'password123',
    ],
    [
      'username' => 'test',    // where clause
      'password' => 'password',
    ]
  )
) {
  echo 'Data has successfully been updated';
} else {
  echo 'Updating data failed';
}
```

This will update the record(s) where the `username` is equal to `test` and the `password` is equal to `password` to `test123` for the `username` and `password123` for the `password`.

### Deleting Data from Database

In order to delete data from a database, follow these steps:

1. Choose a table with the method `DB::table()`.
2. Delete the data with the method `DB::delete()`.

Here's an simple example which deletes the record(s) where the `username` is equal to `test`:

```php
if ($db->table('users')->delete([
  'username' => 'test'  // where clause
])) {
  echo 'Data has successfully been deleted';
} else {
  echo 'Deleting data failed';
}
```

### Custom Logical Operators in Where Clause

Since the release of [version 2.3](https://github.com/jr-cologne/db-class/releases/tag/v2.3.0), a where clause can also have custom logical operators.

This is how a where clause with custom logical operators could look like when retrieving data from a database:

```php
$data = $db->table('users')->select('*', [
  'id' => 1,
  '||',
  'username' => 'test'
])->retrieve();
```

### Using PDO's functionality

Since the database class is extending PDO, you can use the whole functionality of PDO with this class as well.

Just connect to the database using the method `DB::connect()` and after that simply use everything as normal.

An quick example:

```php
// include all files
require_once('vendor/autoload.php');

// use right namespaces
use JRCologne\Utils\Database\DB;
use JRCologne\Utils\Database\QueryBuilder;

// instantiate database class with query builder
$db = new DB(new QueryBuilder);

// connect to database
$db->connect('mysql:host=localhost;dbname=db-class-example;charset=utf8', 'root', 'root');

// prepare query like with PDO class
$stmt = $db->prepare("SELECT * FROM `users`");

// execute query
$stmt->execute();

// fetch all results
$results = $stmt->fetchAll();
```

### API

Looking for a complete overview of each class, property and method of this database class?

Just head over to the [`API.md`](https://github.com/jr-cologne/db-class/blob/master/src/API.md) file where you can find everything you need.

It is located in the source (`src`) folder.

## Further Examples / Stuff for Testing

You want to see further examples of using the database class or you just want to play around with it a little bit?

- You can find further examples in the file [`example/example.php`](https://github.com/jr-cologne/db-class/blob/master/example/example.php).
- To play around with the database class, you can use the database provided in the file [`example/db-class-example.sql`](https://github.com/jr-cologne/db-class/blob/master/example/db-class-example.sql). Just import it in your database client and you are ready to start!

## Contributing

Feel free to contribute to this project! Any kind of contribution is highly appreciated.

In case you have any questions regarding your contribution, do not hesitate to open an Issue.

## Versioning

This project is using the rules of semantic versioning (since version 2). For more information, visit [semver.org](http://semver.org/).

## License

This project is licensed under the [MIT License](https://github.com/jr-cologne/db-class/blob/master/LICENSE).
