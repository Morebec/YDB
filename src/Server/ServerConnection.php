<?php

namespace Morebec\YDB\Server;

use Exception;
use React\EventLoop\Factory as ReactLoopFactory;
use React\Socket\ConnectionInterface;
use React\Socket\Server as ReactSocketServer;

class ServerConnection
{
    /**
     * @var ServerInterface
     */
    private $server;

    /**
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;

    /**
     * @var ReactSocketServer
     */
    private $socket;

    /**
     * ServerConnection constructor.
     * @param ServerInterface $server
     */
    public function __construct(ServerInterface $server)
    {
        $this->server = $server;
    }

    /**
     * Opens a new connection at url
     * @param string $url
     */
    public function open(string $url): void
    {
        $this->loop = ReactLoopFactory::create();
        $this->socket = new ReactSocketServer($url, $this->loop);

        $this->socket->on('connection', function (ConnectionInterface $client) {
            $this->server->onClientConnection($client);

            $client->on('data', function ($rawData) use ($client) {
                $this->server->onDataTransferred($client, $rawData);
            });

            $client->on('end', function () use ($client) {
                $this->server->onClientConnectionEnded($client);
            });

            $client->on('error', function (Exception $e) use ($client) {
                $this->server->onClientConnectionError($client, $e);
            });
        });

        $this->server->onStart();
        $this->loop->run();
    }

    /**
     * Returns the address of this connection
     * @return string
     */
    public function getAddress(): string
    {
        return $this->socket->getAddress();
    }
}
