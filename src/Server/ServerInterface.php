<?php


namespace Morebec\YDB\Server;

use Exception;
use React\Socket\ConnectionInterface;

interface ServerInterface
{
    /**
     * Called when the server is started
     */
    public function onStart(): void;

    /**
     * Called when the server is stopped
     */
    public function onStop(): void;

    /**
     * Called when a connection is established with a client
     * @param ConnectionInterface $client
     */
    public function onClientConnection(ConnectionInterface $client): void;

    /**
     * Called when a client transfers data
     * @param ConnectionInterface $client
     * @param string $rawData
     */
    public function onDataTransferred(ConnectionInterface $client, string $rawData): void;

    /**
     * Called when a client connection ended
     * @param ConnectionInterface $client
     */
    public function onClientConnectionEnded(ConnectionInterface $client): void;

    /**
     * Called when there is an error accepting a new connection from a client
     * @param ConnectionInterface $client
     * @param Exception $exception
     */
    public function onClientConnectionError(ConnectionInterface $client, Exception $exception): void;
}
