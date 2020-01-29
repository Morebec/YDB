<?php


namespace Morebec\YDB\Server;

use Exception;
use Morebec\Collections\HashMap;
use Morebec\YDB\Exception\ServerException;
use Morebec\YDB\Exception\UndefinedServerCommandException;
use Morebec\YDB\InMemory\InMemoryStore;
use Morebec\YDB\Server\Command\CommandFactory;
use React\Socket\ConnectionInterface;

class InMemoryServer implements ServerInterface
{
    /** @var string Version of this server */
    private const VERSION = '0.1.0';

    /**
     * @var InMemoryServerConfig
     */
    private $config;

    /**
     * @var string|string[]|null
     */
    private $address;

    /**
     * @var InMemoryStore
     */
    private $store;

    /**
     * @var CommandFactory
     */
    private $factory;

    public function __construct(InMemoryServerConfig $config)
    {
        $this->config = $config;
        $this->store = new InMemoryStore();
    }

    /**
     * @inheritDoc
     */
    public function onStart(ServerHandler $handler): void
    {
        $this->address = $handler->getSocket()->getAddress();

        $this->factory = new CommandFactory();


        echo sprintf('%s version: %s', $this->getServerName(), self::VERSION)  . PHP_EOL;
        echo 'Listening at ' . $this->address . PHP_EOL;
    }

    /**
     * @inheritDoc
     */
    public function onStop(ServerHandler $handler): void
    {
        echo 'Closing ...';
    }

    /**
     * @inheritDoc
     */
    public function onClientConnection(ConnectionInterface $client): void
    {
        //  $client->write(sprintf('%s version: %s', $this->getServerName(), self::VERSION) . PHP_EOL);
        echo "[Connected {$client->getRemoteAddress()}]" . PHP_EOL;
    }

    /**
     * @inheritDoc
     */
    public function onDataTransferred(ConnectionInterface $client, string $rawData): void
    {
        echo "[{$client->getRemoteAddress()}]: $rawData" . PHP_EOL;

        $data = json_decode($rawData, true, 512, JSON_THROW_ON_ERROR);
        try {
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
        $command->execute($this, $client, $this->store);
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
     * @inheritDoc
     */
    public function getConfig(): ServerConfiguration
    {
        return $this->config;
    }

    /**
     * @return string
     */
    private function getServerName(): string
    {
        return 'Server YDB InMemoryServer';
    }
}
