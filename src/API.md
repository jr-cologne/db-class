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

**Description:** An PDOException instance. Is set when PDO raises an error.

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
