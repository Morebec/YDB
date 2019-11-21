<?php

namespace Morebec\YDB\Service;

use Morebec\YDB\Contract\ColumnInterface;
use Morebec\YDB\Entity\TableSchema;

/**
 * The table updater is responsible for updating tables
 * and their schemas
 */
class TableUpdater
{
    /** @var TableManager */
    private $tableManager;

    function __construct(TableManager $tableManager)
    {
        $this->tableManager = $tableManager;
    }

    /**
     * Adds a column to a table's schema and updates the records
     * @param ColumnInterface $column
     */
    public function addColumn(ColumnInterface $column): void
    {
        // Get records first, or else they wont pass when querying validation
        $records = $this->tableManager->queryAll();

        // Update the schema
        $schema = $this->tableManager->getTableSchema();

        $columns = $schema->getColumns();
        $columns[] = $column;
        $newSchema = new TableSchema($this->getName(), $columns);
        $this->updateSchema($newSchema);
        
        // Update the records
        foreach ($records as $record) {
            $record->setFieldValue($column->getName(), $defaultValue);
            $this->updateRecord($record);
        }
    }
}