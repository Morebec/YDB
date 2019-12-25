<?php

namespace Morebec\YDB\Domain\Command\Table;

use Morebec\YDB\Command\DatabaseCommandInterface;
use Morebec\YDB\Model\Entity\ColumnDefinition;

/**
 * AddTableColumnCommand
 */
class AddTableColumnCommand implements DatabaseCommandInterface
{

    /**
     * @var ColumnDefinition
     */
    private $columnDefinition;

    /**
     * @var string
     */
    private $tableName;

    public function __construct(string $tableName, ColumnDefinition $column)
    {
        $this->tableName = $tableName;
        $this->columnDefinition = $column;
    }

    /**
     * @return ColumnDefinition
     */
    public function getColumnDefinition(): ColumnDefinition
    {
        return $this->columnDefinition;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }
}
