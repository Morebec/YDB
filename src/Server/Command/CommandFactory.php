<?php


namespace Morebec\YDB\Server\Command;

use Closure;
use Morebec\Collections\HashMap;
use Morebec\YDB\Exception\UndefinedServerCommandException;

class CommandFactory
{
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
        $map = $this->buildCommandMap();

        if (!array_key_exists($command, $map)) {
            throw new UndefinedServerCommandException($command);
        }

        $command = $map[$command]($data);

        return $command;
    }

    /**
     * Builds the command map between command names and factory methods
     * @return array<string, Closure>
     */
    private function buildCommandMap(): array
    {
        $map = [];
        $map[CloseConnectionCommand::NAME] = Closure::fromCallable([CloseConnectionCommand::class, 'fromData']);
        return $map;
    }
}
