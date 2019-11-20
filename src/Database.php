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
