<?php 

namespace Morebec\YDB\Command\Record;

use Morebec\YDB\Command\DatabaseCommandInterface;
use Morebec\YDB\Contract\RecordInterface;

/**
 * InsertRecordCommand
 */
class InsertRecordCommand implements DatabaseCommandInterface
{
    /** @var string name of the table */
    private $tableName;

    /** @var RecordInterface record to insert */
    private $record;

    function __construct(string $tableName, RecordInterface $record)
    {
        $this->record = $record;
        $this->tableName = $tableName;
    }

    /**
     * @return RecordInterface
     */
    public function getRecord(): RecordInterface
    {
        return $this->record;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }
}
