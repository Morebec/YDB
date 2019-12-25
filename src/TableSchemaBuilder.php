<?php

namespace Morebec\YDB;

use Morebec\YDB\Domain\Model\Entity\ColumnDefinition;
use Morebec\YDB\Domain\Model\Entity\TableSchema;

/**
 * Helper class to create TableSchemas
 */
class TableSchemaBuilder
{
    /** @var string tableName */
    private $tableName;

    /** @var array array of ColumnInterface objects */
    private $columns;

    private function __construct(string $tableName)
    {
        $this->tableName = $tableName;
        $this->columns = [];
    }

    /**
     * Sets the name of the table
     * @param  string $tableName name of the table
     * @return self            for chaining
     */
    public static function withName(string $tableName): self
    {
        return new static($tableName);
    }

    /**
     * Adds a column to the schema
     * @param  ColumnDefinition $column column definition
     * @return self                    for chaining
     */
    public function withColumn(ColumnDefinition $column): self
    {
        $this->columns[] = $column;
        
        return $this;
    }

    /**
     * Builds the schema and returns it
     * @return TableSchema
     */
    public function build(): TableSchema
    {
        return TableSchema::create($this->tableName, $this->columns);
    }
}
