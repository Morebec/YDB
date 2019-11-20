<?php

namespace Morebec\YDB\Service;

use Morebec\YDB\Contract\QueryInterface;
use Morebec\YDB\Contract\RecordInterface;

/**
 * Class responsible for querying tables
 */
class TableQuerier
{
    /** @var TableManager */
    private $tableManager;

    function __construct(TableManager $tableManager)
    {
        $this->tableManager = $tableManager;
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