<?php 

namespace Morebec\YDB\Command\Table;

use Morebec\YDB\Command\DatabaseCommandInterface;
use Morebec\YDB\Contract\TableSchemaInterface;

/**
 * UpdateTableCommand
 */
class UpdateTableCommand implements DatabaseCommandInterface
{
    /** @var TableSchemaInterface */
    private $tableSchema;

    function __construct(TableSchemaInterface $tableSchema)
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
