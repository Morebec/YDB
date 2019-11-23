<?php

namespace Morebec\YDB\Service;

use Assert\Assertion;
use Morebec\ValueObjects\File\Directory;
use Morebec\ValueObjects\File\File;
use Morebec\YDB\Contract\QueryInterface;
use Morebec\YDB\Contract\QueryResultInterface;
use Morebec\YDB\Contract\TableInterface;
use Morebec\YDB\Contract\TableSchemaInterface;
use Morebec\YDB\Exception\TableNotFoundException;
use Morebec\YDB\Service\Database;

/**
 * Manages operations on tables
 */
class TableManager
{
    /** @var Database */
    private $database;

    /** @var string path to the database */
    private $databasePath;

    /** @var TableLoader */
    private $tableLoader;

    /** @var TableUpdater */
    private $tableUpdater;

    /** @var TableQuerier */
    private $tableQuerier;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->databasePath = $database->getPath();

        $this->tableLoader = new TableLoader($this);
        $this->tableUpdater = new TableUpdater($this);
        $this->tableQuerier = new TableQuerier($this);
    }

    /**
     * Tries to load a table by its name, if it does not exists
     * returns null
     * @param  string $tableName name of the table to load
     * @return TableInterface|null
     */
    public function findTableByName(string $tableName): ?TableInterface
    {
        $table = null;
        try {
            $table = $this->tableLoader->loadTableByName($tableName);
        } catch (TableNotFoundException $e) {
        }

        return $table;
    }

    /**
     * Indicates if a table exists or not
     * @param  string $tableName name of the table
     * @return bool            true if table exists, otherwise false
     */
    public function tableExists(string $tableName): bool
    {
        return $this->tableLoader->tableExists($tableName);
    }

    /**
     * Returns a list of all the table names
     * @return array
     */
    public function getTableNames(): array
    {
        return array_map(static function (TableInterface $table) {
            return $table->getName();
        }, $this->tableLoader->loadTables());
    }
    

    /**
     * Returns the directory containing the tables
     * @return Directory
     */
    public function getTablesDirectory(): Directory
    {
        return Directory::fromStringPath($this->databasePath . '/' . Database::TABLES_DIR_NAME);
    }

    /**
     * Queries a table and returns the a generator to the records
     * that matched the query
     * @param  string         $tableName name of the table to query
     * @param  QueryInterface $query     query
     * @return \Generator
     */
    public function queryTable(
        string $tableName,
        QueryInterface $query
    ): QueryResultInterface {
        return $this->tableQuerier->queryTable($tableName, $query);
    }

    /**
     * Returns the schema of a table  by its name
     * @param  string $tableName name of the table
     * @return TableSchemaInterface
     */
    public function getTableSchema(string $tableName): TableSchemaInterface
    {
        return $this->tableLoader->loadTableSchemaByName($tableName);
    }

    /**
     * Returns the directory of table by its name
     * @param  string $tableName name of the table
     * @return Directory
     */
    public function getTableDirectory(string $tableName): Directory
    {
        // TODO: This line apears in multiple places ...
        // Move this in a centralized place, either tableManager,
        // or maybe a TableLocator class
        return Directory::fromStringPath(
            $this->getTablesDirectory() . "/$tableName"
        );
    }

    /**
     * Logs a message throught the database logger
     * @param  string $level   level
     * @param  string $message message
     * @param  array  $context context
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $this->database->log($level, $message, $context);
    }
}
