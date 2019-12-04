<?php

namespace Morebec\YDB\Exception;

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