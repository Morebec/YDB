<?php


namespace Morebec\YDB\Server;

use Exception;
use React\Socket\ConnectionInterface;

interface ServerInterface
{
    /**
     * Called when the server is started by the handler
     * @param ServerHandler $handler
     */
    public function onStart(ServerHandler $handler): void;

    /**
     * Called when the server is stopped
     * @param ServerHandler $handler
     */
    public function onStop(ServerHandler $handler): void;

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

    /**
     * Returns the server's configuration
     * @return ServerConfiguration
     */
    public function getConfig(): ServerConfiguration;

}
