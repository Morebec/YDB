<?php


namespace Morebec\YDB\Server\Command;

use Morebec\Collections\HashMap;
use Morebec\YDB\Server\InMemoryServer;
use Morebec\YDB\InMemory\InMemoryStore;
use React\Socket\ConnectionInterface;

interface ServerCommandInterface
{
    /**
     * Creates a new instance of this command from data
     * @param HashMap $data
     * @return self
     */
    public static function fromData(HashMap $data): self;

    /**
     * Executes this command
     * @param InMemoryServer $server
     * @param ConnectionInterface $client
     * @param InMemoryStore $store
     * @return mixed
     */
    public function execute(InMemoryServer $server, ConnectionInterface $client, InMemoryStore $store);

    /**
     * Converts this command to an array
     * @return mixed
     */
    public function toArray();
}
