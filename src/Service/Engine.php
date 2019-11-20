<?php 

namespace Morebec\YDB\Service;

use Morebec\YDB\Command\DatabseCommandInterface;
use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\Event\DatabaseEvent;
use Morebec\YDB\Event\Database\DatabaseCreatedEvent;
use Morebec\YDB\Service\DefaultLogger;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * The engine class serves as a service container
 * for the diffrent command handlers of the database
 */
class Engine implements EventSubscriberInterface
{
    /** @var DatabaseConfig */
    private $databaseConfig;

    /** @var DatabaseCommandBus */
    private $commandBus;

    /** @var Filesystem */
    private $filesystem;

    /** @var LoggerInterface|null */
    private $logger;

    /** @var DatabaseEventDispatcher */
    private $eventDispatcher;

    function __construct(DatabaseConfig $config)
    {
        $this->filesystem = new Filesystem();

        // Make path relative
        $config->setDatabasePath(
            $this->filesystem->makePathRelative($config->getDatabasePath(), getcwd())
        );

        $this->databaseConfig = $config;

        $this->commandBus = new DatabaseCommandBus($this);
        $this->eventDispatcher = new DatabaseEventDispatcher($this);
    }

    /**
     * Sets up the logger
     */
    private function setupLogger()
    {
        if($this->logger) return;

        $this->logger = new DefaultLogger($this->databaseConfig);
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

        $context['database_root'] = $this->databaseConfig->getDatabasePath();
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
