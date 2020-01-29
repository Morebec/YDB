<?php


namespace Morebec\YDB\Server\Command;

use Closure;
use Morebec\Collections\HashMap;
use Morebec\YDB\Exception\UndefinedServerCommandException;

class CommandFactory
{
    /** @var array<string, Closure> */
    private $map;

    public function __construct()
    {
        $this->buildCommandMap();
    }

    /**
     * Constructs a command from received data.
     * @param string $command name of the command to build
     * @param HashMap $data
     * @return ServerCommandInterface
     * @throws UndefinedServerCommandException
     */
    public function makeCommand(string $command, HashMap $data): ServerCommandInterface
    {
        if (!array_key_exists($command, $this->map)) {
            throw new UndefinedServerCommandException($command);
        }

        $command = $this->map[$command]($data);

        return $command;
    }

    /**
     * Builds the command map between command names and factory methods
     * @return void
     */
    private function buildCommandMap(): void
    {
        $this->map = [];
        $this->registerCommand(CreateCollectionCommand::NAME, CreateCollectionCommand::class);
        $this->registerCommand(InsertOneDocumentCommand::NAME, InsertOneDocumentCommand::class);
        $this->registerCommand(InsertDocumentsCommand::NAME, InsertDocumentsCommand::class);
        $this->registerCommand(ServerVersionCommand::NAME, ServerVersionCommand::class);
    }

    /**
     * Registers a command factory
     * @param string $commandName
     * @param string $commandClass
     */
    private function registerCommand(string $commandName, string $commandClass): void
    {
        $this->map[$commandName] = Closure::fromCallable([$commandClass, 'fromData']);
    }
}
