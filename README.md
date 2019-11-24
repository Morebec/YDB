# YDB
YDB is a simple PHP utility library to use Yaml as a flat file databases

[![Build Status](https://travis-ci.com/Morebec/YDB.svg?branch=master)](https://travis-ci.com/Morebec/YDB)

YDB is a PHP library used by Morebec in some client projects to allow the use of
yaml files as a database. Some of our clients have technical abilities to view and
edit YAML files and therefore want their projects to save all their data using this
format in such a way that when they require it, they can manually update the data themselves.
It also provides the benifit of applying VCS on the database's data.
YDB therfore provides an easy way to communicate with such a database in a
simple and efficient way. It offers functionalities, like table management, table schema
updates and data indexing for improved performance, as well as a custom SQL like language
called YQL that allows to query the database in a more user-friendly manner.

## Installation
To install the library in a project, add this to your `composer.json` file:

```json
{
    // ...
    "repositories": [
        {
            "url": "https://github.com/Morebec/YDB.git",
            "type": "git"
        }
    ],

    "require": {
        // ...
        "morebec/ydb": "dev-master"
    }
    // ...
}
```

## Usage

### Connecting to a database

```php
$config = new DatabaseConfig('path/to/my/database');
$conn = Database::getConnection($config);
```

### Creating a database
```php
$conn->createDatabase();
```

### Creating a table
```php
$schema = new TableSchemaBuilder()
            ->withName('name-of-the-table')
            ->addColumn(
                    new ColumnBuilder()
                        ->withName('name-of-the-column')
                        ->withType(ColumnType::STRING())
                        ->unique()
                        ->build()
            )
            ->build()
);
```

### Inserting a record
```php
$record = new Record(RecordId::generate(), [
    'name-of-column' => 'value'
]);
$conn->insertRecord($record);
```

### Querying records
```php
$query = QueryBuilder::find('name-of-the-column', Operator::EQUALS(), 'value')
                       ->andWhere('name-of-other-column', Operator::GREATER_THAN(), 30)
                       ->orWhere('column', Operator::IN, [1,2,3,4]);

// Result is a QueryResultInterface Object
$result = $conn->queryTable('name-of-the-table', $query);

// Fetch results one by one
foreach ($result->fetch() as $record) {
    # code...
}

// Fetch all at once and returns them as an array
$result->fetchAll();

// Fetch 10 results at once
$result->limit(10)->fetchAll();

// Returns an array of groups where key is group name and value is
$result->groupBy('column-name'); 

// Find all
$query = QueryBuilder::findAll()->build();
```

## Running Tests

```bash
# Will run all tests including performance tests
composer test

# Will run all tests excluding performance tests
composer test-no-performance
```


