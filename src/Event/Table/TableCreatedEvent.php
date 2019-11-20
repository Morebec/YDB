<?php 

namespace Morebec\YDB\Event\Table;

use Morebec\YDB\Event\DatabaseEvent;

/**
 * Fired when a table is created
 */
class TableCreatedEvent extends DatabaseEvent
{
    const NAME = 'table.created';

    /** @var string name of the table that was cleared */
    private $tableName;

    function __construct(string $tableName)
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
