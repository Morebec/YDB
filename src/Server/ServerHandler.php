<?php


namespace Morebec\YDB\Server;

use Exception;
use React\EventLoop\Factory as ReactLoopFactory;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Server as ReactServer;

/**
 * Class ServerHandler
 * The server handler is a wrapper around react socket to allow interaction
 * between a YDB ServerInterface and a React Socket Server
 */
class ServerHandler
{
    /**
     * @var ServerInterface
     */
    private $server;

    /**
     * @var ReactServer
     */
    private $socket;

    /**
     * @var LoopInterface
     */
    private $loop;

    public function __construct(ServerInterface $server)
    {
        $this->server = $server;
    }

    /**
     * Starts the handled server
     */
    public function start(): void
    {
        $this->loop = ReactLoopFactory::create();
        $this->socket = new ReactServer($this->server->getConfig()->url, $this->loop);

        $server = $this->server;
        $this->socket->on('connection', static function (ConnectionInterface $client) use ($server) {
            $server->onClientConnection($client);

            $client->on('data', static function ($rawData) use ($server, $client) {
                $server->onDataTransferred($client, $rawData);
            });

            $client->on('end', static function () use ($server, $client) {
                $server->onClientConnectionEnded($client);
            });

            $client->on('error', static function (Exception $e) use ($server, $client) {
                $server->onClientConnectionError($client, $e);
            });
        });

        $this->server->onStart($this);
        $this->loop->run();
    }

    /**
     * @return LoopInterface
     */
    public function getLoop(): LoopInterface
    {
        return $this->loop;
    }

    /**
     * @return ReactServer
     */
    public function getSocket(): ReactServer
    {
        return $this->socket;
    }
}
