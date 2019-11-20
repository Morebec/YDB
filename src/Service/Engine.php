<?php 

namespace Morebec\YDB\Service;

use Morebec\YDB\Command\DatabseCommandInterface;
use Morebec\YDB\Service\Database;
use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\Event\DatabaseEvent;
use Morebec\YDB\Event\Database\DatabaseCreatedEvent;
use Morebec\YDB\Service\DatabaseLogger;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * The engine class serves as a service container.
 * It builds the different services and maintains an interface to them. 
 */
class Engine implements EventSubscriberInterface
{
    /** @var Database */
    private $database;

    /** @var DatabaseCommandBus usd to issue commands */
    private $commandBus;

    /** @var Filesystem used for file manipulation by the different services */
    private $filesystem;

    /** @var LoggerInterface|null */
    private $logger;

    /** @var DatabaseEventDispatcher */
    private $eventDispatcher;

    /** @var TableManager */
    private $tableManager;

    function __construct(DatabaseConfig $config)
    {
        // Initialize filesystem
        $this->filesystem = new Filesystem();

        // Initialize Database
        $this->database = new Database($config, $this);

        // Initialize command bus
        $this->commandBus = new DatabaseCommandBus($this->database);

        // Initialize event dispatcher
        $this->eventDispatcher = new DatabaseEventDispatcher($this);

        // Try to configure logger
        if ($this->database->exists()) {
            $this->setupLogger();
        }
    }

    /**
     * Sets up the logger
     */
    private function setupLogger(): void
    {
        $this->logger = new DatabaseLogger($this->database->getPath());
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
            $this->logException($e);
            throw $e;
        }
    }

    /**
     * Dispatches an event through the event dispatcher
     * @param  string                 $name  name of the event to dispatch
     * @param  DatabaseEvent $event event to dispatch
     */
    public function dispatchEvent(string $name, DatabaseEvent $event): void
    {
        try {
            $this->eventDispatcher->dispatch($name, $event);
        } catch (\Exception $e) {
            $this->logException($e);
            throw $e;
        }
    }

    /**
     * Logs a message through the logger
     * @param  LogLevel $level   level of the message
     * @param  string   $message message
     * @param  array    $context optional context data
     */
    public function log(string $level, string $message, array $context = []): void
    {
        if(!$this->logger) return;

        $context['database_root'] = $this->database->getPath();
        $this->logger->log($level, $message, $context);
    }

    /**
     * Logs an exception and throws it again
     * @param  Throwable $e Throwable exception
     */
    private function logException(\Throwable $e): void
    {
        $this->log(LogLevel::CRITICAL, 'There was an exception: ' . $e->getMessage(), [
            'exception_class' => get_class($e)
        ]);
    }

    /**
     * Returns the Database service instance
     * @return Database
     */
    public function getDatabase(): Database
    {
        return $this->database;
    }

    /**
     * @return DatabaseConfig
     */
    public function getDatabaseConfig(): DatabaseConfig
    {
        return $this->database->getConfig();
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * Called when the database has been created
     */
    public function onDatabaseCreated(): void
    {
        $this->setupLogger();
        $this->log(LogLevel::INFO, 'Database created');
    }

    public static function getSubscribedEvents()
    {
        return [
            DatabaseCreatedEvent::NAME => 'onDatabaseCreated'
        ];
    }
}
