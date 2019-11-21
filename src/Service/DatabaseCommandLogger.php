<?php 

namespace Morebec\YDB\Service;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\Service\Database;

/**
 * DatabaseCommandLogger
 */
class DatabaseCommandLogger extends Logger
{
    /**
     * Constructs an instance of the database logger
     * @param string $databasePath path to the database
     */
    function __construct(string $databasePath)
    {
        parent::__construct('command');
        $logsDir = $databasePath . "/" . Database::LOGS_DIR_NAME;
        
        $this->pushHandler(new RotatingFileHandler($logsDir . '/database_command.log', Logger::DEBUG));
    }
}