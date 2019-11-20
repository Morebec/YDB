# Quickstart
This document explains the basics of the library and its uses.

## Database structure
A YDB database follows a precise file hierarchy:

database_root/              # Root directory of the database
    - logs/                 # Directory containing the log files
    - bin/                  # Contains the binaries for console commands
    - tables/               # Directory containg all the tables and their data
        - table1/           # Directory of a table (in this case table named 'table1')
            - indexes/      # Directory containing the generated indexes for the table's records
                - value.idx # An index file for a specific value
            - schema.yaml   # The schema of the table
            - record.yaml   # Yaml file representing a single record where the name 
                              of the file is its id

## Creating a connection to the database
First, you need to create a configuration object. This configuration object allows to specify some parameters like where is located the database and the logger to use.

```php
$config = new DatabaseConfig('path/to/my/database');
// Override default logger
$config->setLogger(new MyLogger());
```

Getting a connection object
```php
$conn = Database::getConnection($config);
```

This connection object is the main entry point to manipulating the database:
```php
$conn->createTable($tableName, $schema);
$conn->updateTable($tableName, $newSchema);
$conn->tableExists($tableName);
```

## Table creation and manipulation

### Table creation
Every table is configured by using a table schema. The schema defines the name of the table and the columns and there indexes:

```php
$schema = new TableSchemaBuilder()
            ->withName('name-of-the-table')
            ->addColumn(
                    new ColumnBuilder()
                        ->withName('name-of-the-column')
                        ->withType(ColumnType->STRING())
                        ->unique()
                        ->build()
            )
);
```
A schema can also be defined by a YAML file (in the table's directory):

```yaml
table_name: name-of-the-table
columns:
    - { name: id, type: string, primary: true }
    - { name: a_column, type: string, indexed: false, unique: false }
```

**Note**: A table schema must always have a primary column, that is used for uniquely identifying the records on the file system. By default table schema auto generates a primary column called id. To override this behaviour, use the following method:

```php 
$tableSchemaBuilder->setPrimaryColumnName('my-primary');
```

#### Creating a table programatically
To create a table programatically follow these steps:
    1. Define the table schema:
    ```php
    $schema = new TableSchemaBuilder()
                ->withName('name-of-the-table')
                ->addColumn(
                        new ColumnBuilder()
                            ->withName('name-of-the-column')
                            ->withType(ColumnType->STRING())
                            ->unique()
                            ->build()
                )
    );
    ```
    1. Create the table using the connection and the schema
    ```php
    $conn->createTable($schema);
    ```
#### Creating a table using a console command
To create a table using the console commands, follow these steps:

**NOTE: TODO**

#### Creating a table manually
To create a table manually you must:
    1. In the tables directory, create a directory for the table.
    ```
    $ cd database_root/tables && mkdir my-table
    ```
    1. Define the schema in a `schema.yaml` file:
    ```yaml
    table_name: name-of-the-table
    columns:
        - { name: id, type: string, unique: true }
        - { name: a_column, type: string, indexed: false, unique: false }

    ```

### Table Schema update
To update a schema

**NOTE**: It is recommended to update table schemas programatically or using console commands as it ensures that the records are updated according to the new schema and also regenerates their indexes. If you decide to update a table schema manually, you will need to edit every record file to make sure it follows the new schema and will need to regenerate the indexes either programatically, or using a console command.

## Record creation and manipulation

### Creating records programatically
Creating a record is done by using the `Record` object and a `DatabaseConnection` object:
$record = new Record(

); $conn->insertRecord('table-name', $record); ```

Creating records using a console command
NOTE: TODO

Creating records manually
NOTE: TODO