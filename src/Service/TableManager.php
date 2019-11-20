<?php 

namespace Morebec\YDB\Service;

use Assert\Assertion;
use Morebec\ValueObjects\File\Directory;
use Morebec\ValueObjects\File\File;
use Morebec\YDB\Exception\TableNotFoundException;

/**
 * Manages operations on tables
 */
class TableManager
{
    /** @var string path to the database */
    private $databasePath;

    /** @var TableLoader */
    private $tableLoader;

    function __construct(string $databasePath)
    {
        $this->databasePath = $databasePath;
        $this->tableLoader = new TableLoader($this->getTablesDirectory());
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
        return $this->tableLoader->tableExists();
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
    private function getTablesDirectory(): Directory
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
    public function queryRecordsFromTable(string $tableName, QueryInterface $query): \Generator
    {
        
    }

    /**
     * Queries a single record from the database matching a query
     * or return null if none found.
     * If more than one records match the query, returns an exception
     * @param  string         $tableName name of the table
     * @param  QueryInterface $query     query
     * @return RecordInterface|null
     */
    public function queryOneRecordFromTable(string $tableName, QueryInterface $query): ?RecordInterface
    {
        # code...
    }
}