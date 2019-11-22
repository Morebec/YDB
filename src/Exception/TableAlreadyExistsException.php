<?php

namespace Morebec\YDB\Exception;

/**
 * TableAlreadyExistsException
 */
class TableAlreadyExistsException extends DatabaseException
{
    public function __construct(string $tableName)
    {
        parent::__construct("Table '$tableName' already exists");
    }
}
