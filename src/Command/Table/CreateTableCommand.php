<?php

namespace Morebec\YDB\Command\Table;

use Morebec\YDB\Command\DatabaseCommandInterface;
use Morebec\YDB\Contract\TableSchemaInterface;

/**
 * CreateTableCommand
 */
class CreateTableCommand implements DatabaseCommandInterface
{
    /** @var TableSchemaInterface */
    private $tableSchema;

    public function __construct(TableSchemaInterface $tableSchema)
    {
        $this->tableSchema = $tableSchema;
    }

    /**
     * @return TableSchemaInterface
     */
    public function getTableSchema(): TableSchemaInterface
    {
        return $this->tableSchema;
    }
}
