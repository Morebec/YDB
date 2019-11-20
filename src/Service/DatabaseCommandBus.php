<?php 

namespace Morebec\YDB\Service;

use Morebec\YDB\CommandHandler\Database\ClearDatabaseCommandHandler;
use Morebec\YDB\CommandHandler\Database\CreateDatabaseCommandHandler;
use Morebec\YDB\CommandHandler\Database\DeleteDatabaseCommandHandler;
use Morebec\YDB\CommandHandler\Table\CreateTableCommandHandler;
use Morebec\YDB\Command\Database\ClearDatabaseCommand;
use Morebec\YDB\Command\Database\CreateDatabaseCommand;
use Morebec\YDB\Command\Database\DeleteDatabaseCommand;
use Morebec\YDB\Command\Table\CreateTableCommand;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

/**
 * DatabaseCommandBus
 */
class DatabaseCommandBus extends MessageBus
{
    /**
     * Constructs an instance of DatabaseCommandBus
     * @param Database $database database
     */
    function __construct(Database $database)
    {
        parent::__construct([
            new HandleMessageMiddleware(
                new HandlersLocator(
                    $this->buildHandlersList($database)
                )
            ),
        ]);
    }

    /**
     * Builds the list of commands and their associated handlers
     * and returns it
     * @param  Database $database database
     * @return array
     */
    private function buildHandlersList(Database $database): array
    {
        return [
            // Database Commands
            CreateDatabaseCommand::class => [new CreateDatabaseCommandHandler($database)],
            DeleteDatabaseCommand::class => [new DeleteDatabaseCommandHandler($database)],
            ClearDatabaseCommand::class => [new ClearDatabaseCommandHandler($database)],

            // Table Commands
            CreateTableCommand::class => [new CreateTableCommandHandler($database)]
        ];
    }
}