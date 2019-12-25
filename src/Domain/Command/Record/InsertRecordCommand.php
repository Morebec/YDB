<?php

namespace Morebec\YDB\Domain\Command\Record;

use Morebec\YDB\Command\DatabaseCommandInterface;
use Morebec\YDB\Contract\Record;
use Morebec\YDB\Model\Entity\Record;

/**
 * InsertRecordCommand
 */
class InsertRecordCommand implements DatabaseCommandInterface
{
    /** @var string name of the table */
    private $tableName;

    /** @var Record record to insert */
    private $record;

    public function __construct(string $tableName, Record $record)
    {
        $this->record = $record;
        $this->tableName = $tableName;
    }

    /**
     * @return Record
     */
    public function getRecord(): Record
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
