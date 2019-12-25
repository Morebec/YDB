<?php

namespace Morebec\YDB\Domain\Exception;

use Morebec\YDB\Contract\RecordIdInterface;

/**
 * RecordInvalidException
 */
class RecordInvalidException extends DatabaseException
{
    public function __construct(string $tableName, RecordIdInterface $recordId)
    {
        parent::__construct("Invalid record '$recordId' for table '$tableName'");
    }
}
