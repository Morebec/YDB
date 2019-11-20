<?php 

namespace Morebec\YDB\Service;

use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\Command\DatabseCommandInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * The engine class serves as a service container
 * for the diffrent command handlers of the database
 */
class Engine
{
    /** @var DatabaseConfig */
    private $databaseConfig;

    /** @var DatabaseCommandBus */
    private $commandBus;

    /** @var Filesystem */
    private $filesystem;

    function __construct(DatabaseConfig $config)
    {
        $this->databaseConfig = $config;
        $this->commandBus = new DatabaseCommandBus($this);
        $this->logger = $config->getLogger();
        $this->filesystem = new Filesystem();
    }

    /**
     * Dispatches a command through the command bus
     * @param  DatabseCommandInterface $command command to dispatch
     */
    public function dispatchCommand(DatabseCommandInterface $command): void
    {
        $this->commandBus->dispatch($command);
    }

    /**
     * Logs a message through the logger
     * @param  LogLevel $level   level of the message
     * @param  string   $message message
     * @param  array    $context optional context data
     */
    public function log(LogLevel $level, string $message, array $context = [])
    {
        if(!$this->logger) return;

        $this->logger-log($level, $message, $context);
    }

    /**
     * @return DatabaseConfig
     */
    public function getDatabaseConfig(): DatabaseConfig
    {
        return $this->databaseConfig;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }
}
