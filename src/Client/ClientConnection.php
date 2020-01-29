<?php


namespace Morebec\YDB\Client;

use Exception;
use Morebec\YDB\Client\ClientException;
use Psr\Log\LoggerInterface;
use React\EventLoop\Factory as ReactLoopFactory;
use React\EventLoop\LoopInterface;
use React\Promise\FulfilledPromise;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use React\Promise\RejectedPromise;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;

/**
 * Class ClientConnection
 * Wrapper around ReactPHP to perform sync requests
 */
class ClientConnection
{
    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var Connector
     */
    private $socket;
    /**
     * @var FulfilledPromise|Promise|PromiseInterface|RejectedPromise|null
     */
    private $promise;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * Opens a connection to a given url
     * @param string $url
     * @return LoopInterface
     */
    public function open(string $url): void
    {
        $this->loop = ReactLoopFactory::create();
        $this->socket = new Connector($this->loop);

        $this->promise = $this->socket->connect($url);
    }

    /**
     * Sends data to the server and waits for a response
     * @param string $data
     * @return string|null
     */
    public function send(string $data): ?string
    {
        $receivedData = null;
        $this->promise->then(
            function (ConnectionInterface $server) use ($data, &$receivedData) {
                $server->on('data', function ($rawData) use (&$receivedData) {
                    $receivedData = $rawData;
                    $this->loop->stop();
                });
                $this->log("Sending: $data");
                $server->write($data);
            },
            function (Exception $e) {
                throw new ClientException($e->getMessage(), $e->getCode(), $e);
            }
        );
        $this->loop->run();

        $this->log("Received: {$receivedData}");
        return $receivedData;
    }

    public function close(): void
    {
        $this->loop->stop();
    }

    private function log(string $message)
    {
        if (!$this->logger) {
            return;
        }

        $this->logger->debug('[YDB Connection] ' . $message);
    }
}
