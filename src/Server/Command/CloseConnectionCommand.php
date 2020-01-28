<?php


namespace Morebec\YDB\Server\Command;

use Morebec\Collections\HashMap;
use Morebec\YDB\InMemory\InMemoryServer;
use Morebec\YDB\InMemory\InMemoryStore;
use React\Socket\ConnectionInterface;

class CloseConnectionCommand implements ServerCommandInterface
{
    public const NAME = 'close_connection';

    public static function fromData(HashMap $data): self
    {
        return new static();
    }

    /**
     * @inheritDoc
     */
    public function execute(InMemoryServer $server, ConnectionInterface $client, InMemoryStore $store)
    {
        $client->end();
    }
}
