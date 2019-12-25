<?php

namespace Morebec\YDB\legacy\Service;

use Morebec\ValueObjects\File\Directory;
use Morebec\YDB\Contract\QueryInterface;
use Morebec\YDB\Entity\QueryPlan\IdScanStrategy;
use Morebec\YDB\Entity\QueryPlan\IndexScanStrategy;
use Morebec\YDB\Entity\QueryPlan\MultiStrategy;
use Morebec\YDB\Entity\QueryPlan\QueryPlan;
use Morebec\YDB\Entity\QueryPlan\QueryPlanStrategy;
use Morebec\YDB\Entity\QueryPlan\TableScanStrategy;
use Morebec\YDB\Entity\TableSchema;
use Morebec\YDB\YQL\PYQL;

/**
 * Provides methods to execute a QueryPlan
 */
class QueryPlanExecutor
{
    /** @var TableManager */
    private $tableManager;

    /** @var RecordLoader */
    private $recordLoader;

    public function __construct(TableManager $tableManager)
    {
        $this->tableManager = $tableManager;
        $this->recordLoader = new RecordLoader();
    }

    /**
     * Executes a Query plan and returns the associated records
     * @param  QueryPlan $plan plan
     * @return \Generator
     */
    public function execute(
        string $tableName,
        QueryInterface $query,
        QueryPlan $plan
    ): \Generator {
        $strategy = $plan->getStrategy();
        $records = $this->getStrategyRecords($tableName, $strategy);

        foreach ($records as $record) {
            if (PYQL::evaluateQueryForRecord($query, $record)) {
                yield $record;
            }
        }
    }

    /**
     * Returns the records associated with a strategy
     * @param  QueryPlanStrategy $strategy strategy
     * @return Generator
     */
    private function getStrategyRecords(
        string $tableName,
        QueryPlanStrategy $strategy
    ):\Generator {
        if ($strategy instanceof TableScanStrategy) {
            return $this->doTableScan($tableName);
        }

        if ($strategy instanceof IndexScanStrategy) {
            return $this->doIndexScan($tableName, $strategy);
        }

        if ($strategy instanceof IdScanStrategy) {
            return $this->doIdScan($tableName, $strategy);
        }

        if ($strategy instanceof MultiStrategy) {
            return $this->doMultiScan($tableName, $strategy);
        }
    }


    /**
     * Does a table scan of all the records and returns a generator
     * @return \Generator
     */
    private function doTableScan(string $tableName): \Generator
    {
        $dir = $this->tableManager->getTableDirectory($tableName);
        $files = $dir->getFiles();
        foreach ($files as $file) {
            if ($file->getExtension() != 'yaml') {
                continue;
            }
            if ($file->getBasename() === TableSchema::SCHEMA_FILE_NAME) {
                continue;
            }
                        
            $data = $this->recordLoader->load($file);

            yield $data;
        }
    }

    /**
     * Does an index scan for a given index and returns the associated Records
     * @param  string $indexName index
     * @return \Generator
     */
    public function doIndexScan(
        string $tableName,
        IndexScanStrategy $strategy
    ): \Generator {
        // TODO: Implement indexes
        yield from $this->doTableScan($tableName);
    }

    /**
     * Does a filename scan for an array of ids
     */
    public function doIdScan(
        string $tableName,
        IdScanStrategy $strategy
    ): \Generator {
        // TODO: Implement filename scan
        yield from $this->doTableScan($tableName);
    }

    /**
     * Executes a multiscan strategy
     * @param  string        $tableName name of the table
     * @param  MultiStrategy $strategy  strategy
     * @return \Generator
     */
    public function doMultiScan(
        string $tableName,
        MultiStrategy $multi
    ): \Generator {
        foreach ($multi->getStragies() as $s) {
            yield from $this->getStrategyRecords($tableName, $s);
        }
    }
}
