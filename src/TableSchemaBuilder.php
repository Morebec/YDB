<?php 

namespace Morebec\YDB;

use Morebec\YDB\Contract\ColumnInterface;
use Morebec\YDB\Contract\TableSchemaInterface;
use Morebec\YDB\Entity\TableSchema;

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
     * @param  ColumnInterface $column column definition
     * @return self                    for chaining
     */
    public function withColumn(ColumnInterface $column): self
    {
        $this->columns[] = $column;
        
        return $this;
    }

    public function build(): TableSchemaInterface
    {
        return new TableSchema($this->tableName, $this->columns);
    }
}
