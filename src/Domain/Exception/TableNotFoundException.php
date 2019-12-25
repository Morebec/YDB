<?php

namespace Morebec\YDB\Domain\Exception;

/**
 * TableNotFoundException
 */
class TableNotFoundException extends DatabaseException
{
    public function __construct(string $tableName)
    {
        parent::__construct("Table '$tableName' was not found");
    }
}
