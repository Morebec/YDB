<?php

namespace Morebec\YDB\Domain\CommandHandler\Table;

use Morebec\YDB\Command\Table\AddTableColumnCommand;
use Morebec\YDB\Service\Database;

/**
 * AddTableColumnCommandHandler
 */
class AddTableColumnCommandHandler
{
    /**
     * @var Database
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }
    
    public function __invoke(AddTableColumnCommand $command)
    {
        $tableName = $command->getTableName();
        $column = $command->getColumnDefinition();
        
        // Ensure table exists
        if(!$this->database->tableExists($tableName)) {
            throw new TableNotFoundException($tableName);
        }
        
        $this->database->addTableColumn($tableName, $column);
    }
}
