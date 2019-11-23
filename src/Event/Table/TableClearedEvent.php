<?php

namespace Morebec\YDB\Event\Table;

use Morebec\YDB\Event\DatabaseEvent;

/**
 * Fired when a table is cleared
 */
class TableClearedEvent extends DatabaseEvent
{
    const NAME = 'table.cleared';

    /** @var string name of the table that was cleared */
    private $tableName;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }
}
