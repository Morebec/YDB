<?php 

namespace Morebec\YDB;

use Psr\Log\LoggerInterface;

/**
 * DatabaseConfig
 */
class DatabaseConfig
{   
    /**
     * Directory where the database should be located
     * @var string
     */
    private $databasePath;

    /** @var LoggerInterface|null logger */
    private $logger;

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
    public function setDatabasePath(string $databasePath): string
    {
        $this->databasePath = $databasePath;

        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return self
     */
    public function setLogger(?LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }
}
