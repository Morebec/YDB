<?php


namespace Morebec\YDB\Domain\Model\Entity;

use Morebec\YDB\Entity\TableSchema;

/**
 * Represents a table.
 * A Table is used to organize records with a similar structure
 */
class Table
{
    private $schema;

    private function __construct(TableSchema $tableSchema)
    {
        $this->schema = $tableSchema;
    }

    public static function create(TableSchema $tableSchema): self
    {
        return new static($tableSchema);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->schema->getTableName();
    }

    /**
     * @return TableSchema
     */
    public function getSchema(): TableSchema
    {
        return $this->schema;
    }
}