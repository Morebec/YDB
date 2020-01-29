<?php


namespace Morebec\YDB\Server\Command;

use Morebec\Collections\HashMap;
use Morebec\YDB\InMemory\InMemoryStore;
use Morebec\YDB\Server\Server;
use React\Socket\ConnectionInterface;

/**
 * Class ServerVersionCommand
 * @package Morebec\YDB\Server\Command
 * This command returns the version of the server
 */
class ServerVersionCommand implements ServerCommandInterface
{
    public const NAME = 'server_version';

    /**
     * @inheritDoc
     */
    public static function fromData(HashMap $data): ServerCommandInterface
    {
        return new static();
    }

    /**
     * @inheritDoc
     */
    public function execute(Server $server, ConnectionInterface $client, InMemoryStore $store)
    {
        return $server->getVersion();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [];
    }
}
