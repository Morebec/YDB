<?php

namespace Morebec\YDB;

use Generator;
use Morebec\YDB\Command\Database\ClearDatabaseCommand;
use Morebec\YDB\Command\Database\CreateDatabaseCommand;
use Morebec\YDB\Command\Database\DeleteDatabaseCommand;
use Morebec\YDB\Command\Record\InsertRecordCommand;
use Morebec\YDB\Command\Table\AddTableColumnCommand;
use Morebec\YDB\Command\Table\CreateTableCommand;
use Morebec\YDB\Command\Table\UpdateTableCommand;
use Morebec\YDB\Contract\ColumnInterface;
use Morebec\YDB\Contract\QueryInterface;
use Morebec\YDB\Contract\QueryResultInterface;
use Morebec\YDB\Contract\Record;
use Morebec\YDB\Contract\TableSchemaInterface;
use Morebec\YDB\Entity\TableSchema;
use Morebec\YDB\Service\Database;

/**
 * The Database connection is the main entry point
 * tot the library for end users
 */
class DatabaseConnection
{
    /** @var Database database */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Creates the database on the filesystem
     */
    public function createDatabase(): void
    {
        $this->database->dispatchCommand(new CreateDatabaseCommand());
    }

    /**
     * Deletes the database from the filesystem
     */
    public function deleteDatabase(): void
    {
        $this->database->dispatchCommand(new DeleteDatabaseCommand());
    }

    /**
     * Clears the whole database
     */
    public function clearDatabase(): void
    {
        $this->database->dispatchCommand(new ClearDatabaseCommand());
    }

    /**
     * Creates a table from a schema
     * @param  TableSchema $schema schema
     */
    public function createTable(TableSchema $schema): void
    {
        $this->database->dispatchCommand(new CreateTableCommand($schema));
    }

    /**
     * Updates the schema of a table
     * @param  string      $tableName name of the table
     * @param  TableSchema $schema    schema
     */
    public function updateTable(string $tableName, TableSchema $schema): void
    {
        $this->database->dispatchCommand(new UpdateTableCommand($tableName, $schema));
    }

    /**
     * Drops a table
     * @param  string $tableName name of the table
     */
    public function dropTable(string $tableName): void
    {
        $this->database->dispatchCommand(new DropTableCommand($tableName));
    }
    
    /**
     * Returns the schema of a specific table
     * @param string $tableName name of the table
     * @return TableSchemaInterface
     */
    public function getTableSchema(string $tableName): TableSchemaInterface
    {
        return $this->database->getTableSchema($tableName);
    }

    /**
     * Indicates if a table exists
     * @param  string $tableName name of the table
     * @return bool            true if table exists, otherwise false
     */
    public function tableExists(string $tableName): bool
    {
        $tableManager = $this->database->getTableManager();
        return $tableManager->tableExists();
    }

    /**
     * Returns a list of all the tables
     * @return array
     */
    public function getTableNames(): array
    {
        return $this->database->getTableNames();
    }
    
    /**
     * Adds a new column on a table
     * @param ColumnInterface $column
     * @return void
     */
    public function addTableColumn(string $tableName, ColumnInterface $column): void
    {
        $this->database->dispatchCommand(
                new AddTableColumnCommand($tableName, $column)
        );
    }

    /**
     * Inserts a record in a table
     * @param  string          $tableName  name of the table
     * @param  Record $record $record
     */
    public function insertRecord(string $tableName, Record $record): void
    {
        $this->database->dispatchCommand(new InsertRecordCommand($tableName, $record));
    }

    /**
     * Updates a record in the database
     * @param  string          $tableName name of the table
     * @param  Record $record    record
     */
    public function updateRecord(string $tableName, Record $record): void
    {
        # code...
    }

    /**
     * Deletes a record in the database
     * @param  Record $record record
     */
    public function deleteRecord(string $tableName, Record $record): void
    {
        # code...
    }

    /**
     * Queries a table and returns a generatlr that can be fetched
     * until there are no more records
     * @param  string         $tableName name of the table
     * @param  QueryInterface $query     query
     * @return Generator
     */
    public function query(string $tableName, QueryInterface $query): QueryResultInterface
    {
        return $this->database->queryTable($tableName, $query);
    }
}
