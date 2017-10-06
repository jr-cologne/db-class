# db-class

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
require_once('vendor/autoload.php');
```

### Manual Installation

1. Download the ZIP file of this project
2. Unzip it and move everything to your own project directory.
3. Include all files of the database class into your project like that:

```php
require_once('path/to/db-class/src/DB.php');
require_once('path/to/db-class/src/QueryBuilder.php');
require_once('path/to/db-class/src/Exceptions/UnsupportedKeywordException.php');
```

Now you should be ready to start!

## Basic Usage

### Namespace

Before instantiating the class, always make sure to use the right namespaces:

```php
use JRCologne\Utils\Database\DB;
use JRCologne\Utils\Database\QueryBuilder;
```

### Instantiate Class (`DB::__construct()`)

To be able to use the class, you have to instantiate it.

Just do this:

```php
$db = new DB(new QueryBuilder);
```

### Where can I find the documentation?

There is currently no real documentation. But don't worry, it's coming soon!

Until then, you can simply take a look at the code and you will probably understand most things as I commented every property and method of the database class.

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

## Further Examples / Stuff for Testing
You want to see further examples for using the database class or you just want to play around with it a little bit?

- You can find further examples in the file [`example/example.php`](https://github.com/jr-cologne/db-class/blob/master/example/example.php).
- To play around with the database class, you can use the database provided in the file [`example/db-class-example.sql`](https://github.com/jr-cologne/db-class/blob/master/example/db-class-example.sql). Just import it in your database client and you are ready to start!

## Contributing
Feel free to contribute to this project! It would be awesome for me if somebody contributes to it.

So don't be shy and start coding! If you want to make sure that I like your idea, you can contact me by an Issue.

But if you decide to contribute to this project, keep in mind that finally, it is my choice to merge your Pull Request or not, so also be prepared for a negative decision.

## License
This project is licensed under the [MIT License](https://github.com/jr-cologne/db-class/blob/master/LICENSE).
