<?php

namespace Morebec\YDB\Domain\Exception;

/**
 * TableSchemaNotFoundException
 */
class TableSchemaNotFoundException extends DatabaseException
{
    public function __construct(string $tableName, string $schemaFile)
    {
        parent::__construct("Table schema not found on '$tableName' at '$schemaFile'");
    }
}
