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
        $this->filesystem = new Filesystem();

        // Make path relative
        $config->setDatabasePath(
            $this->filesystem->makePathRelative($config->getDatabasePath(), getcwd())
        );

        $this->databaseConfig = $config;
        $this->commandBus = new DatabaseCommandBus($this);

        // Set the logger or use the default one if none provided
        $this->logger = $config->getLogger();
        if(!$this->logger) {
            // The default logger automatically creates a logs directory
            // even if the database was not created yet. 
            // This is problematic. Find a workaround
            $this->logger = new DefaultLogger($config);
        }
    }

    /**
     * Dispatches a command through the command bus
     * @param  DatabseCommandInterface $command command to dispatch
     */
    public function dispatchCommand(DatabseCommandInterface $command): void
    {
        try {
            $this->commandBus->dispatch($command);
        } catch (\Exception $e) {
            $this->log(LogLevel::CRITICAL, 'There was an exception: ' . $e->getMessage(), [
                'exception_class' => get_class($e)
            ]);
            throw $e;
        }
    }

    /**
     * Logs a message through the logger
     * @param  LogLevel $level   level of the message
     * @param  string   $message message
     * @param  array    $context optional context data
     */
    public function log(string $level, string $message, array $context = [])
    {
        if(!$this->logger) return;

        $context['database_root'] = $this->databaseConfig->getDatabasePath();
        $this->logger->log($level, $message, $context);
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
