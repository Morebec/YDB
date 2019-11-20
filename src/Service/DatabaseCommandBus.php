<?php 

namespace Morebec\YDB\Service;

use Morebec\YDB\CommandHandler\Database\CreateDatabaseCommandHandler;
use Morebec\YDB\CommandHandler\Database\DeleteDatabaseCommandHandler;
use Morebec\YDB\Command\Database\CreateDatabaseCommand;
use Morebec\YDB\Command\Database\DeleteDatabaseCommand;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

/**
 * DatabaseCommandBus
 */
class DatabaseCommandBus extends MessageBus
{
    function __construct(Engine $engine)
    {
        parent::__construct([
            new HandleMessageMiddleware(
                new HandlersLocator(
                    $this->buildHandlersList($engine)
                )
            ),
        ]);
    }

    /**
     * Builds the list of commands and their associated handlers
     * and returns it
     * @param  Engine $engine engine
     * @return array
     */
    private function buildHandlersList(Engine $engine): array
    {
        return [
            CreateDatabaseCommand::class => [new CreateDatabaseCommandHandler($engine)],
            DeleteDatabaseCommand::class => [new DeleteDatabaseCommandHandler($engine)],
        ];
    }
}