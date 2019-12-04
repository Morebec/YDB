<?php

namespace Morebec\YDB\Service;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\Service\Database;
use Morebec\YDB\Enum\LoggerChannel;

/**
 * DatabaseLogger
 */
class DatabaseLogger extends Logger
{

    private $loggers = [];

    /**
     * Constructs an instance of the database logger
     * @param string $databasePath path to the database
     */
    public function __construct(string $databasePath)
    {
        $channels = LoggerChannel::getValues();
        foreach($channels as $channel){
            $this->loggers[$channel] = new Logger($channel);
            $logsDir = $databasePath . "/" . Database::LOGS_DIR_NAME;
            $this->pushHandler(new RotatingFileHandler($logsDir . "/".$channel."log", Logger::DEBUG));
        }

    }
}
