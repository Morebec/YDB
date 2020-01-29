<?php


namespace Morebec\YDB\Server\Command;

use Morebec\Collections\HashMap;
use Morebec\YDB\Document;
use Morebec\YDB\DocumentId;
use Morebec\YDB\InMemory\InMemoryStore;
use Morebec\YDB\Server\Server;
use React\Socket\ConnectionInterface;

class InsertDocumentsCommand implements ServerCommandInterface
{
    public const NAME = 'insert_documents';
    /**
     * @var string
     */
    private $collectionName;
    /**
     * @var array
     */
    private $documents;

    public function __construct(string $collectionName, array $documents)
    {
        $this->collectionName = $collectionName;
        $this->documents = $documents;
    }

    /**
     * @inheritDoc
     */
    public static function fromData(HashMap $data): ServerCommandInterface
    {
        $collectionName = $data->get('collection_name');

        $documents = [];
        foreach ($data->get('documents') as $document) {
            $documents[] = new Document(DocumentId::fromString($document['_id']), $document);
        }

        return new static($collectionName, $documents);
    }

    /**
     * @inheritDoc
     */
    public function execute(Server $server, ConnectionInterface $client, InMemoryStore $store)
    {
        $store->insertMany($this->collectionName, $this->documents);
        return new SuccessStatus();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'command' => self::NAME,
            'collection_name' => $this->collectionName,
            'documents' => array_map(static function (Document $doc) {
                return $doc->toArray();
            }, $this->documents)
        ];
    }
}
