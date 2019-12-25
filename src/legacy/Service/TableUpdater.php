<?php

namespace Morebec\YDB\legacy\Service;

use Morebec\YDB\Contract\ColumnInterface;
use Morebec\YDB\Contract\TableSchemaInterface;
use Morebec\YDB\Entity\TableSchema;

/**
 * The table updater is responsible for updating tables
 * and their schemas
 */
class TableUpdater
{
    /** @var TableManager */
    private $tableManager;

    public function __construct(TableManager $tableManager)
    {
        $this->tableManager = $tableManager;
    }

    /**
     * Adds a column to a table's schema and updates the records
     * @param ColumnInterface $column
     */
    public function addColumn(ColumnInterface $column): void
    {
        // Get the current schema
        $schema = $this->tableManager->getTableSchema();
        
        // Get a list of the current schema's columns
        // and add the new one
        $columns = $schema->getColumns();
        $columns[] = $column;
        
        // Create new schema with the new column
        $newSchema = new TableSchema($schema->getName(), $columns);
        
        // Update schema
        $this->updateSchema($newSchema);
    }
    
    public function dropColumn(ColumnInterface $column): void
    {
        // Get the current schema
        $schema = $this->tableManager->getTableSchema();

        // Get a list of the current schema's columns
        // and drop the column 
        $columns = array_filter(
            $schema->getColumns(), 
            static function(ColumnInterface $col) use ($column) {
                $col->getName() !== $column->getName();
            }
        );
        
        // Create new schema with the new column
        $newSchema = new TableSchema($schema->getName(), $columns);

        // Update schema
        $this->updateSchema($newSchema);
    }

    /**
     * Updates the schema of a table
     * @param  TableSchemaInterface $newSchema new schema
     */
    public function updateSchema(TableSchemaInterface $newSchema): void
    {
        // Preload records, as they wont pass validation because
        // of their unstable schema
        $records = $this->tableManager->queryAll();
        
        // Update the schema file
        
        // Update the records
        foreach ($records as $record) {
            // Make sure there is a value for each of the schema's column
            foreach ($newSchema->getColumns() as $column) {
                if (!$record->hasField($column->getName())) {
                    $record->setFieldValue(
                            $column->getName(),
                            $column->getDefaultValue()
                    );
                }
            }

            // Check if there are no columns that were dropped that would need
            // to be removed from the record
            foreach ($record->getFields() as $field) {
                if (!$newSchema->columnWithNameExists($field)) {
                    $record->removeField($field);
                }
            }

            $this->updateRecord($record);
        }
    }
}
