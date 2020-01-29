<?php


namespace Morebec\YDB\Server;

use Exception;
use Morebec\Collections\HashMap;
use Morebec\YDB\DocumentStoreInterface;
use Morebec\YDB\Server\ServerException;
use Morebec\YDB\Exception\UndefinedServerCommandException;
use Morebec\YDB\InMemory\InMemoryStore;
use Morebec\YDB\Server\Command\CommandFactory;
use React\Socket\ConnectionInterface;

class Server implements ServerInterface
{
    /** @var string Version of this server */
    private const VERSION = '0.1.0';

    /**
     * @var ServerConfiguration
     */
    private $config;

    /**
     * @var InMemoryStore
     */
    private $store;

    /**
     * @var CommandFactory
     */
    private $factory;

    /**
     * @var ServerConnection
     */
    private $connection;

    public function __construct(ServerConfiguration $config, DocumentStoreInterface $store)
    {
        $this->config = $config;
        $this->store = $store;
    }

    /**
     * Starts the server
     */
    public function start(): void
    {
        $this->connection = new ServerConnection($this);
        $this->connection->open($this->config->url);
    }

    /**
     * @inheritDoc
     */
    public function onStart(): void
    {
        $address = $this->connection->getAddress();
        $this->factory = new CommandFactory();

        echo sprintf('%s version: %s', $this->getServerName(), self::VERSION)  . PHP_EOL;
        echo 'Listening at ' . $address . PHP_EOL;
    }

    /**
     * @inheritDoc
     */
    public function onStop(): void
    {
        echo 'Stopping ...';
    }

    /**
     * @inheritDoc
     */
    public function onClientConnection(ConnectionInterface $client): void
    {
        echo "[Connected {$client->getRemoteAddress()}]" . PHP_EOL;
    }

    /**
     * @inheritDoc
     */
    public function onDataTransferred(ConnectionInterface $client, string $rawData): void
    {
        echo "[{$client->getRemoteAddress()}]: $rawData" . PHP_EOL;

        try {
            $data = json_decode($rawData, true, 512, JSON_THROW_ON_ERROR);
            $this->processData($client, new HashMap($data));
        } catch (\Exception $e) {
            $errorData = [
                'status' => $e->getCode(),
                'error' => [
                    'message' => $e->getMessage()
                ]
            ];
            $client->write(json_encode($errorData, JSON_THROW_ON_ERROR, 512));
        }
    }

    /**
     * Processes incoming data from a client
     * @param ConnectionInterface $client
     * @param HashMap $data
     * @throws ServerException
     * @throws UndefinedServerCommandException
     */
    private function processData(ConnectionInterface $client, HashMap $data): void
    {
        if (!$data->containsKey('command')) {
            throw new ServerException('Missing command name');
        }
        $command = $this->factory->makeCommand($data->get('command'), $data);
        $result = $command->execute($this, $client, $this->store);
        $client->write(json_encode($result, JSON_THROW_ON_ERROR, 512));
    }

    /**
     * @inheritDoc
     */
    public function onClientConnectionEnded(ConnectionInterface $client): void
    {
        echo "[Disconnected {$client->getRemoteAddress()}]" . PHP_EOL;
    }

    /**
     * @inheritDoc
     */
    public function onClientConnectionError(ConnectionInterface $client, Exception $exception): void
    {
        $clientAddress = $client->getRemoteAddress();
        echo "Error establishing connection with {$clientAddress}: {$exception->getMessage()}";
    }

    /**
     * Returns the version of the server
     * @return string
     */
    public function getVersion(): string
    {
        return sprintf('%s %s', $this->getServerName(), self::VERSION);
    }

    /**
     * @return string
     */
    private function getServerName(): string
    {
        return 'YDB Server';
    }
}
