<?php 

namespace Morebec\YDB\CommandHandler\Database;

use Morebec\YDB\Command\Database\ClearDatabaseCommand;
use Morebec\YDB\Service\Database;
use Psr\Log\LogLevel;

/**
 * Handles the clearing of a database
 */
class ClearDatabaseCommandHandler
{
    /** @var Database */
    private $database;

    function __construct(Database $database)
    {
        $this->database = $database;   
    }

    public function __invoke(ClearDatabaseCommand $command)
    {
        $this->database->log(LogLevel::INFO, 'Clearing database ...');

        foreach ($this->database->getTableNames() as $tableName) {
            $this->database->log(LogLevel::INFO, "Clearing table '$tableName' ...");

            $this->database->clearTable($tableName);

            $this->database->log(LogLevel::INFO, "Table '$tableName' cleared");
        }

        $this->database->log(LogLevel::INFO, 'Database cleared.');
    }
}
