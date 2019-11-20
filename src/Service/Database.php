<?php 

namespace Morebec\YDB\Service;

use Morebec\ValueObjects\File\Directory;
use Morebec\ValueObjects\File\Path;
use Morebec\YDB\Command\DatabaseCommandInterface;
use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\DatabaseConnection;
use Morebec\YDB\Event\DatabaseEvent;
use Morebec\YDB\Service\Engine;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Database
 */
class Database
{
    /** Name of the directory containing the tables */
    const TABLES_DIR_NAME = 'tables';

    /** Name of the directory containing the binary files */
    const BIN_DIR_NAME = 'bin';

    /** Name of the directory containing the logs */
    const LOGS_DIR_NAME = 'logs';

    /** @var DatabaseConfig */
    private $config;

    /** @var Engine */
    private $engine;

    /** @var TableManager */
    private $tableManager;

    /**
     * Constructs a Database instance
     * @param DatabaseConfig $config configuration of the database
     * @param Engine         $engine engine service
     */
    public function __construct(DatabaseConfig $config, Engine $engine)
    {
        $this->config = $config;
        $this->engine = $engine;
        $this->tableManager = new TableManager($config->getDatabasePath());
    }

    /**
     * Dispatches a command on the database's engine
     * @param  DatabaseCommandInterface $command command
     */
    public function dispatchCommand(DatabaseCommandInterface $command): void
    {
        $this->engine->dispatchCommand($command);
    }

    /**
     * Dispatches an event on the database's engine
     * @param  DatabaseEvent $event event
     */
    public function dispatchEvent(string $eventName, DatabaseEvent $event): void
    {
        $this->engine->dispatchEvent($eventName, $event);
    }

    /**
     * Returns wether or not the database exists at its configured location
     * @return bool true if it exists, otherwise false
     */
    public function exists(): bool
    {
        $dir = new Directory($this->getPath());
        return $dir->exists();
    }

    /**
     * Queries a table on the database and returns the matching records
     * as a Generator
     * @param  string         $tableName name of the table
     * @param  QueryInterface $query     query
     * @return \Generator
     */
    public function query(string $tableName, QueryInterface $query): \Generator
    {
        return $this->tableManager->queryRecordsFromTable($tableName, $query);   
    }

    /**
     * Queries a table on the database for one single results and returns it or null
     * if nothing was found.
     * If more than one results match the query, throws an exception
     * @param  string         $tableName name of the table
     * @param  QueryInterface $query     query
     * @return RecordInterface|null
     */
    public function queryOne(string $tableName, QueryInterface $query): RecordInterface
    {
        return $this->tableManager->queryOneRecordFromTable($tableName, $query);
    }

    /**
     * Indicates if a table exists or not
     * @param  string $tableName name of the table
     * @return bool            true if it exists, otherwise false
     */
    public function tableExists(string $tableName): bool
    {
        return $this->tableManager->tableExists($tableName);
    }

    /**
     * Returns a list of all the table names
     * @return array
     */
    public function getTableNames(): array
    {
        return $this->tableManager->getTableNames();
    }

    /**
     * Logs a message to the database's logger
     * @param  string $logLevel level of the message
     * @param  string $message  message of the log
     * @param  array  $context  contextual data
     */
    public function log(string $logLevel, $message, array $context = []): void
    {
        $this->engine->log($logLevel, $message, $context);
    }

    /**
     * Returns the configuration of the database
     * @return DatabaseConfig
     */
    public function getConfig(): DatabaseConfig
    {
        return $this->config;
    }

    /**
     * Returns the path to the database
     * @return Path
     */
    public function getPath(): Path
    {
        return new Path($this->config->getDatabasePath());
    }

    /**
     * Returns the filesystem handler
     * @return Filesystem
     */
    public function getFileSystem(): Filesystem
    {
        return $this->engine->getFileSystem();
    }

    /**
     * Establishes a connection with the database and returns it
     * @param  DatabaseConfig $config config
     * @return DatabaseConnection
     */
    public static function getConnection(DatabaseConfig $config): DatabaseConnection
    {
        $engine = new Engine($config);

        $database = $engine->getDatabase();
        $conn = new DatabaseConnection($database);

        return $conn;
    }
}
