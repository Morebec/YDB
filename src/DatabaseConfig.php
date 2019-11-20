<?php 

namespace Morebec\YDB;

use Psr\Log\LoggerInterface;

/**
 * DatabaseConfig
 */
class DatabaseConfig
{   
    /**
     * Relative path to the directory where the database should be located
     * @var string
     */
    private $databasePath;

    function __construct(string $databasePath)
    {
        $this->databasePath = $databasePath;
    }

    /**
     * @return string
     */
    public function getDatabasePath(): string
    {
        return $this->databasePath;
    }

    /**
     * @param string $databasePath
     *
     * @return self
     */
    public function setDatabasePath(string $databasePath): self
    {
        $this->databasePath = $databasePath;

        return $this;
    }
}
