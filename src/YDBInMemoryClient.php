<?php


namespace Morebec\YDB;

use Morebec\Collections\HashMap;
use Morebec\YDB\Client\ClientConnection;
use Morebec\YDB\Client\ClientHandler;
use Morebec\YDB\Client\YDBClientConfiguration;
use Morebec\YDB\Client\YDBClientInterface;
use Morebec\YDB\Server\Command\CreateCollectionCommand;
use Morebec\YDB\Server\Command\ServerCommandInterface;
use Morebec\YDB\YQL\Query\ExpressionQuery;
use Morebec\YDB\YQL\Query\QueryResult;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;

class YDBInMemoryClient implements YDBClientInterface
{
    /**
     * @var YDBInMemoryClientConfiguration
     */
    private $config;

    /**
     * @var ClientConnection
     */
    private $connection;

    /**
     * YDBInMemoryClient constructor.
     * @param YDBInMemoryClientConfiguration $config
     */
    public function __construct(YDBInMemoryClientConfiguration $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function insertDocument(string $collectionName, Document $document): void
    {
        $this->sendCommand(new InsertDocumentCommand($collectionName, $document));
    }

    /**
     * @inheritDoc
     */
    public function updateOneDocument(string $collectionName, Document $document): void
    {
        $this->sendCommand(new UpdateOneDocumentCommand($collectionName, $document));
    }

    /**
     * @inheritDoc
     */
    public function updateDocuments(string $collectionName, array $documents): void
    {
        $this->sendCommand(new UpdateDocumentsCommand($collectionName, $documents));
    }

    /**
     * @inheritDoc
     */
    public function executeQuery(ExpressionQuery $query): QueryResult
    {
        $this->sendCommand(new ExecuteQueryCommand($query));
    }

    /**
     * @inheritDoc
     */
    public function deleteDocument(ExpressionQuery $query): QueryResult
    {
        $this->sendCommand(new DeleteDocumentCommand($query));
    }

    /**
     * @inheritDoc
     */
    public function createCollection(string $collectionName): void
    {
        $this->sendCommand(new CreateCollectionCommand($collectionName));
    }

    /**
     * @inheritDoc
     */
    public function dropCollection(string $collectionName): void
    {
        $this->sendCommand(new DropCollectionCommand($collectionName));
    }

    /**
     * @inheritDoc
     */
    public function clearCollection(string $collectionName): void
    {
        $this->sendCommand(new ClearCollectionCommand);
    }

    /**
     * Sends a command to the server
     * @param ServerCommandInterface $command
     */
    private function sendCommand(ServerCommandInterface $command): void
    {
        $data = $command->toArray();
        $this->connection->send(json_encode($data, JSON_THROW_ON_ERROR, 512));
    }

    /**
     * @inheritDoc
     */
    public function onDataReceived(ConnectionInterface $server, HashMap $data): void
    {
        if ($data->get('code') === 0) {
            throw new ClientException($data->get('error')['message'], $data->get('code'));
        }
        $server->close();
    }

    /**
     * Connects to the server
     */
    public function connect(): void
    {
        $this->connection = new ClientConnection();
        $this->connection->open($this->config->url);
    }
}
