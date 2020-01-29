<?php


namespace Morebec\YDB\Server\Command;

use Morebec\Collections\HashMap;
use Morebec\YDB\Document;
use Morebec\YDB\DocumentId;
use Morebec\YDB\InMemory\InMemoryStore;
use Morebec\YDB\Server\Server;
use React\Socket\ConnectionInterface;

/**
 * Inserts a document in a collection
 */
class InsertOneDocumentCommand implements ServerCommandInterface
{
    public const NAME = 'insert_document';

    /**
     * @var string
     */
    private $collectionName;
    /**
     * @var Document
     */
    private $document;

    public function __construct(string $collectionName, Document $document)
    {
        $this->collectionName = $collectionName;
        $this->document = $document;
    }

    /**
     * @inheritDoc
     */
    public static function fromData(HashMap $data): ServerCommandInterface
    {
        $collectionName = $data->get('collection_name');
        $document = new Document(DocumentId::fromString($data->get('document')['_id']), $data->get('document'));
        return new static($collectionName, $document);
    }

    /**
     * @inheritDoc
     */
    public function execute(Server $server, ConnectionInterface $client, InMemoryStore $store)
    {
        $store->insertOne($this->collectionName, $this->document);
        return new SuccessStatus();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'collection_name' => $this->collectionName,
            'document' => $this->document->toArray()
        ];
    }
}
