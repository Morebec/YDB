<?php 

namespace Morebec\YDB\Service;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Morebec\YDB\Database;
use Morebec\YDB\DatabaseConfig;

/**
 * DefaultLogger
 */
class DefaultLogger extends Logger
{
    
    function __construct(DatabaseConfig $config)
    {
        parent::__construct('ydb');
        $root = $config->getDatabasePath();
        $logsDir = $root . "/" . Database::LOGS_DIR_NAME;
        $this->pushHandler(new RotatingFileHandler($logsDir, Logger::WARNING));
    }
}