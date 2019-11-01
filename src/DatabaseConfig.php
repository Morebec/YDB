<?php 

namespace Morebec\YDB;

use Morebec\ValueObjects\File\Directory;

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

    /** @var bool indicates if logging should be enabled or not */
    private $loggingEnabled = true;

    /** @var bool indicates if logging should be enabled */
    private $indexingEnabled = true;

    function __construct(Directory $databasePath)
    {
        $this->databasePath = $databasePath;
    }

    /**
     * @return Directory
     */
    public function getDatabasePath(): Directory
    {
        return $this->databasePath;
    }

    /**
     * @param Directory $databasePath
     *
     * @return self
     */
    public function setDatabasePath(Directory $databasePath): Directory
    {
        $this->databasePath = $databasePath;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLoggingEnabled(): bool
    {
        return $this->loggingEnabled;
    }

    /**
     * @param bool $loggingEnabled
     *
     * @return self
     */
    public function enableLogging(bool $loggingEnabled): self
    {
        $this->loggingEnabled = $loggingEnabled;

        return $this;
    }

    /**
     * Disables logging
     * 
     * @return self
     */
    public function disableLogging(): self
    {
        $this->loggingEnabled = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIndexingEnabled(): bool
    {
        return $this->indexingEnabled;
    }

    /**
     * @param bool $indexingEnabled
     *
     * @return self
     */
    public function enableIndexing(bool $indexingEnabled): self
    {
        $this->indexingEnabled = $indexingEnabled;

        return $this;
    }

    /**
     * Disables indexing
     * 
     * @return self
     */
    public function disableIndexing(): self
    {
        $this->indexingEnabled = false;

        return $this;
    }
}
