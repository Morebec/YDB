<?php

namespace Morebec\YDB\Service;

use Morebec\YDB\Contract\QueryInterface;
use Morebec\YDB\Contract\RecordInterface;
use Morebec\YDB\Contract\TableSchemaInterface;
use Morebec\YDB\Entity\QueryResultInterface;
use Morebec\YDB\Enum\QuerySource;
use Morebec\YDB\Exception\TableNotFoundException;

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
     * Queries a table and returns the QueryRecord
     * that matched the query
     * @param  string         $tableName name of the table to query
     * @param  QueryInterface $query     query
     * @return \Generator
     */
    public function queryTable(
        string $tableName, 
        QueryInterface $query
    ): QueryResultInterface
    {
        // A Query is based on criteria that belong to two groups:
        //  - and: Every criterion must return true
        //  - or: At least one criterion must return true
        // We need to determine the records that need to be loaded
        // for the query evaluation, that is, if we ever need to load
        // all records to make the checks. This result is called the source.
        // It can therefore either be [all|index]
        // The rules are
        // Both "ands" and "ors" groups of criteria must be checked.
        // 
        // If at least one criterion of the "and" group can work with
        // indexes, the source of the "and" group shall be index
        // Inversely, if at least one "or" criterion requires all records,
        // the source of the "or" group shall be all
        // 
        // Finally if both groups can rely on indexes only we will load form the indexes
        
        // First validate that the table in the query exists
        
        if(!$this->tableManager->tableExists($tableName)) {
            throw new QueryException("Invalid query: table '$tableName' does not exist");
        }

        $schema = $this->tableManager->getTableSchema($tableName);

        $andSource = $this->getAndSource($query->getAndCriteria());
        $orSource = $this->getOrSource($query->getOrCriteria());
        $finalSource = $andSource == QuerySource::INDEX && 
                       $orSource == QuerySource::INDEX ?
                       new QuerySource(QuerySource::INDEX) : 
                       new QuerySource(QuerySource::ALL)
        ;

        if($finalSource == QuerySource::ALL) {

        }
    }




    private function getAndSource(TableSchemaInterface $schema, array $criteria): QuerySource
    {
        foreach ($criteria as $criterion) {
            $col = $this->getColumnByName($c->getField());
            if(!$col) {
                throw new QueryException("Invalid query: table '$tableName' does not exist");
            }

            if($col->isIndexed()) {
                return new QuerySource(QuerySource::INDEX);
            }
        }

        return new QuerySource(QuerySource::ALL);
    }

    private function getOrSource(TableSchemaInterface $schema, array $criteria): QuerySource
    {
        foreach ($criteria as $criterion) {
            $col = $this->getColumnByName($c->getField());
            if(!$col) {
                throw new QueryException("Invalid query: table '$tableName' does not exist");
            }

            if(!$col->isIndexed()) {
                return new QuerySource(QuerySource::ALL);
            }
        }

        return new QuerySource(QuerySource::INDEX);
    }
}