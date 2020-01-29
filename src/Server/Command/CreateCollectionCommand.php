<?php


namespace Morebec\YDB\Server\Command;

use Morebec\Collections\HashMap;
use Morebec\YDB\Server\ServerException;
use Morebec\YDB\InMemory\InMemoryStore;
use Morebec\YDB\Server\Server;
use React\Socket\ConnectionInterface;

class CreateCollectionCommand implements ServerCommandInterface
{
    public const NAME = 'create_collection';

    public $collectionName;

    public function __construct(string $collectionName)
    {
        $this->collectionName = $collectionName;
    }

    /**
     * @inheritDoc
     */
    public static function fromData(HashMap $data): ServerCommandInterface
    {
        if (!$data->get('collection_name')) {
            throw new ServerException('Invalid command');
        }

        return new static($data->get('collection_name'));
    }

    /**
     * @inheritDoc
     */
    public function execute(Server $server, ConnectionInterface $client, InMemoryStore $store)
    {
        $store->createCollection($this->collectionName);

        return new SuccessStatus();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
          'command' => self::NAME,
          'collection_name' => $this->collectionName
        ];
    }
}
