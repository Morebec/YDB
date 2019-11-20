<?php 

namespace Morebec\YDB;

use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\DatabaseConnection;
use Morebec\YDB\Service\Engine;

/**
 * Database
 */
class Database
{
    /** Name of the directory containing the tables */
    const TABLE_DIR_NAME = 'tables';

    /** Name of the directory containing the binary files */
    const BIN_DIR_NAME = 'bin';

    /** Name of the directory containing the logs */
    const LOGS_DIR_NAME = 'logs';

    private function __construct()
    {
    }

    /**
     * Establishes a connection with the database and returns it
     * @param  DatabaseConfig $config config
     * @return DatabaseConnection]
     */
    public function getConnection(DatabaseConfig $config): DatabaseConnection
    {
        $engine = new Engine($config);
        $conn = new DatabaseConnection($engine);

        return $conn;
    }
}
