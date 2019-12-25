<?php

namespace Morebec\YDB\legacy\Service;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\Service\Database;

/**
 * DatabaseLogger
 */
class DatabaseLogger extends Logger
{
    /**
     * Constructs an instance of the database logger
     * @param string $databasePath path to the database
     */
    public function __construct(string $databasePath)
    {
        parent::__construct('default');
        $logsDir = $databasePath . "/" . Database::LOGS_DIR_NAME;
        
        $this->pushHandler(new RotatingFileHandler($logsDir . '/database.log', Logger::DEBUG));
    }
}
