<?php 

namespace Morebec\YDB\Exception;

/**
 * TableAlreadyExistsException
 */
class TableAlreadyExistsException extends DatabaseException
{
    function __construct(string $tableName)
    {
        parent::__construct("Table '$tableName' already exists");
    }
}