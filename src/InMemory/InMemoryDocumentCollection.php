<?php


namespace Morebec\YDB\InMemory;

use Morebec\Collections\HashMap;
use Morebec\YDB\Document;
use Morebec\YDB\DocumentCollectionInterface;

class InMemoryDocumentCollection implements DocumentCollectionInterface
{
    /**
     * @var HashMap
     */
    private $documents;

    public function __construct()
    {
        $this->documents = new HashMap();
    }

    /**
     * @inheritDoc
     */
    public function insertDocument(Document $document): void
    {
        $this->documents->put($document->getId(), $this->cloneDocument($document));
    }

    /**
     * @inheritDoc
     */
    public function updateDocument(Document $document): void
    {
        $this->documents->put($document->getId(), $this->cloneDocument($document));
    }

    /**
     * @inheritDoc
     */
    public function removeDocument(Document $document): void
    {
        $this->documents->remove($document->getId());
    }

    /**
     * @inheritDoc
     */
    public function getDocuments(): array
    {
        return $this->documents->getValues();
    }

    /**
     * Clone in order for documents not be automatically persisted when there in memory reference change
     * @param Document $document
     * @return Document
     */
    private function cloneDocument(Document $document): Document
    {
        return clone $document;
    }

    /**
     * @inheritDoc
     */
    public function hasIndexOnField(string $fieldName): bool
    {
        // TODO: Implement hasIndexOnField() method.
    }
}