<?php


namespace Morebec\YDB\Server\Command;

use Morebec\Collections\HashMap;
use Morebec\YDB\Exception\ServerException;
use Morebec\YDB\InMemory\InMemoryStore;
use Morebec\YDB\Server\InMemoryServer;
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
    public function execute(InMemoryServer $server, ConnectionInterface $client, InMemoryStore $store)
    {
        $store->createCollection($this->collectionName);

        $client->write('ok');
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
          'command' => self::NAME,
          'collection_name' => $this->collectionName
        ];
    }
}
