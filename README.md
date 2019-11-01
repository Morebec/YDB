# YDB
YDB is a simple PHP utility library to use Yaml as a flat file databases

## Usage

### Create a Database
In order to create a database do the following:

```php
$database = new Database(Directory::fromStringPath('path/to/the/database'));
```

### Create a Table
Tables are created using a `TableSchema` that defines the columns of the table.

A table schema is created this way
```php
$schema = new TableSchema('test-query-record', [
    new Column('id', ColumnType::STRING(), true /* indexed */),
    new Column('first_name', ColumnType::STRING()),
    new Column('last_name', ColumnType::STRING()),
    new Column('age', ColumnType::INTEGER())
]);
```

And to create the table: 

```php
$table = $this->database->createTable($schema);
```

### Create a Record
TO add a new record to the database, one must use a Record Object.
A record can be constructed as follows:

```php
$r = new Record(
    RecordId::generate(), // This will generate a Uuidv4 id.
    [
        'first_name' => 'Barney',
        'last_name' => 'Stinson',
        'age' => 31
    ]
);

// And then add the record to the table
$table->addRecord($record);
);
```

**Note on ids**: If you want to have a different type of id, simply create a class 
implementing the `RecordIdInterface`.

### Query a Record
In order to query a record, one must create a Query Object:

```php

// Multiple Criteria
$r = $table->queryOne(
    new Query([
        new Criterion('first_name', Operator::STRICTLY_EQUAL(), 'James'),
        new Criterion('last_name', Operator::STRICTLY_EQUAL(), 'Bond'),
        new Criterion('age', Operator::GREATER_OR_EQUAL(), 42)
    ]);
);

// Helper static methods
$r = $table->queryOne(Query::findById($record->getId()));

$r = $table->queryOne(
    Query::findByField('first_name', Operator::STRICTLY_EQUAL(), 'James')
);


```

However the easiest way is to use the Query builder:

```php
// This query will find all records that have 'James Bond' as their full name or
// that are 35 years old or less
$query = QueryBuilder::find('first_name', Operator::STRICTLY_EQUAL(), 'James')
                     ->and('last_name', Operator::STRICTLY_EQUAL(), 'Bond')
                     ->or('age', Operator::LESS_OR_EQUAL(), 35)
                     ->build()
;
$r = $table->query($query);
```
