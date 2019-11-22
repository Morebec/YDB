<?php

namespace Morebec\YDB;

use Morebec\YDB\Command\Database\ClearDatabaseCommand;
use Morebec\YDB\Command\Database\CreateDatabaseCommand;
use Morebec\YDB\Command\Database\DeleteDatabaseCommand;
use Morebec\YDB\Command\Record\InsertRecordCommand;
use Morebec\YDB\Command\Table\CreateTableCommand;
use Morebec\YDB\Contract\RecordInterface;
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
        # code...
    }


    /**
     * Inserts a record in a table
     * @param  string          $tableName  name of the table
     * @param  RecordInterface $record $record
     */
    public function insertRecord(string $tableName, RecordInterface $record): void
    {
        $this->database->dispatchCommand(new InsertRecordCommand($tableName, $record));
    }

    /**
     * Updates a record in the database
     * @param  string          $tableName name of the table
     * @param  RecordInterface $record    record
     */
    public function updateRecord(string $tableName, RecordInterface $record): void
    {
        # code...
    }

    /**
     * Deletes a record in the database
     * @param  RecordInterface $record record
     */
    public function deleteRecord(string $tableName, RecordInterface $record): void
    {
        # code...
    }

    /**
     * Queries a table and returns a generatlr that can be fetched
     * until there are no more records
     * @param  string         $tableName name of the table
     * @param  QueryInterface $query     query
     * @return \Generator
     */
    public function query(string $tableName, QueryInterface $query): \Generator
    {
        # code...
    }

    /**
     * Queries a single result from the database and returns the record or null
     * if none could be found. If there are more than one results matching
     * the query, throws an exception.
     * @param  string         $tableName name of the table
     * @param  QueryInterface $query     query
     * @return RecordInterface|null
     */
    public function queryOne(string $tableName, QueryInterface $query): ?RecordInterface
    {
        # code...
    }
}
