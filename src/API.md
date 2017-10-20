# API

This is a complete overview of each property and method of this database class.


## DB Class

The main and only class you actively work with in order to interact with a database.


### Namespace

```php
JRCologne\Utils\Database
```


### Properties

#### `$query_builder`

**Description:** The instance of the query builder.

**Visibility:** `protected`

**Data Type:** `QueryBuilder`

**Default Value:** -


#### `$default_options`

**Description:** The default options of this class for PDO, which are passed into the method `PDO::__construct()`.

**Visibility:** `protected`

**Data Type:** `array`

**Default Value:**

```php
[
  PDO::ATTR_PERSISTENT => true,
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]
```


#### `$results`

**Description:** The results of the query from the method `DB::select()`.

**Visibility:** `protected`

**Data Type:** `mixed` (based on `PDO::FETCH_MODE`), default: `array`

**Default Value:** -


#### `$query_failed`

**Description:** Indicator whether the query of the method DB::select() failed.

**Visibility:** `protected`

**Data Type:** `boolean`

**Default Value:** `false`


#### `$pdo_exception`

**Description:** A `PDOException` instance. Is set when PDO raises an error.

**Visibility:** `protected`

**Data Type:** `PDOException`

**Default Value:** -


### Methods

#### `DB::__construct()`

**Description:**

```php
DB::__construct(QueryBuilder $query_builder)
```

Instantiates the class and sets `DB::query_builder` to the passed in `QueryBuilder` instance.

**Visibility:** `public`

**Parameters:**

`QueryBuilder $query_builder`: The `QueryBuilder` instance

**Return Values:** -


#### `DB::connect()`

**Description:**

```php
DB::connect(string $dsn, string $username = null, string $password = null, array $options = [])
```

Creates a connection to a specified database by PDO.

**Visibility:** `public`

**Parameters:**

`string $dsn`: The data source name of the method `PDO::__construct()`

`string $username = null`

`string $password = null`

`array $options = []`: An array of options that should be passed into the method `PDO::__construct()`

**Return Values:** `boolean` true on success, false on failure


#### `DB::table()`

**Description:**

```php
DB::table(string $table)
```

Sets the table to use for the following query.

**Visibility:** `public`

**Parameters:**

`string $table`: The table which should be used for the following query.

**Return Values:** `DB` The `DB` instance


#### `DB::select()`

**Description:**

```php
DB::select(string $columns, array $where = [], int $fetch_mode = PDO::FETCH_ASSOC)
```

Executes a SELECT query and fetches data from a database.

**Visibility:** `public`

**Parameters:**

`string $columns`

`array $where`:

An associative array of the format `column => value` which is used for the where clause of the query.

Example, which essentially translates to the where clause ``WHERE `username` = 'test' && `password` = 'password'``:

```php
[
  'username' => 'test',
  'password' => 'password'
]
```

`int $fetch_mode = PDO::FETCH_ASSOC`: The wished PDO fetch mode.

**Return Values:** `DB` The `DB` instance


#### `DB::retrieve()`

**Description:**

```php
DB::retrieve(string $keyword = 'all')
```

Retrieves a particular amount of the data selected and fetched by the method `DB::select()`.

**Visibility:** `public`

**Parameters:**

`string $keyword = 'all'`: The keyword which specifies the amount of data to retrieve.

Supported Keywords:

- `all` (retrieves the whole dataset)
- `first` (retrieves the first record)

**Return Values:** `mixed` based on method called by `$keyword`


#### `DB::all()`

**Description:**

```php
DB::all()
```

Returns the whole dataset given in `DB::results`.

**Visibility:** `protected`

**Parameters:** -

**Return Values:** `mixed`

On success: `array` (default) or other type based on `PDO::FETCH_MODE`

On failure: `boolean` false


#### `DB::first()`

**Description:**

```php
DB::first()
```

Returns the first record given in DB::results.

**Visibility:** `protected`

**Parameters:** -

**Return Values:** `mixed`

On success: `array` (default) or other type based on `PDO::FETCH_MODE`

On failure: `boolean` false


#### `DB::insert()`

**Description:**

```php
DB::insert(array $data)
```

Inserts data into a database.

**Visibility:** `public`

**Parameters:**

`array $data`: An associative array of the format `column => value` with the data to insert.

**Return Values:** `boolean` true on success, false on failure


#### `DB::multi_insert()`

**Description:**

```php
DB::multi_insert(string $columns, array $data)
```

Inserts multiple rows of data into a database.

**Visibility:** `public`

**Parameters:**

`string $columns`

`array $data`: A two-dimensional array with an associative array of the format `column => value` with the data to insert for each row.

**Return Values:** `mixed` true on success, 0 if at least one record could be inserted, false on failure


#### `DB::update()`

**Description:**

```php
DB::update(array $data, array $where = [])
```

Updates data of a database.

Attention: If you do not provide a where clause, all records of the table will be updated!

**Visibility:** `public`

**Parameters:**

`array $data`: An associative array of the format `column => value` with the new data.

`array $where = []`:

An associative array of the format `column => value` which is used for the where clause of the query.

Example, which essentially translates to the where clause ``WHERE `username` = 'test' && `password` = 'password'``:

```php
[
  'username' => 'test',
  'password' => 'password'
]
```

**Return Values:** `boolean` true on success, false on failure


#### `DB::delete()`

**Description:**

```php
DB::delete(array $where = [])
```

Deletes data from a database.

Attention: If you do not provide a where clause, all records of the table will be deleted!

**Visibility:** `public`

**Parameters:**

`array $where = []`:

An associative array of the format `column => value` which is used for the where clause of the query.

Example, which essentially translates to the where clause ``WHERE `username` = 'test' && `password` = 'password'``:

```php
[
  'username' => 'test',
  'password' => 'password'
]
```

**Return Values:** `boolean` true on success, false on failure


#### `DB::getPDOException()`

**Description:**

```php
DB::getPDOException()
```

Returns the `PDOException` set when PDO raised an error.

**Visibility:** `public`

**Parameters:** -

**Return Values:** `PDOException` A `PDOException` instance


#### `DB::formatWhereData()`

**Description:**

```php
DB::formatWhereData(array $where)
```

Formats the data for the where clause which is passed into the method `PDOStatement::execute()`.

This is done in order to prevent issues/conflicts when a particular column should be updated or deleted and is also part of the where clause.

For example, the following would not work correctly without the help of this method:

```php
$db->update(
  [ 'username' => 'new_username' ],
  [ 'username' => 'old_username' ]
);
```

For consistency, the method is used in every sort of query method with a potential where clause.

**Visibility:** `public`

**Parameters:**

`array $where`: An associative array of the format `column => value`.

**Return Values:** `array`


## QueryBuilder Class

The query builder class which is creating all queries for this database class.


### Namespace

```php
JRCologne\Utils\Database
```
