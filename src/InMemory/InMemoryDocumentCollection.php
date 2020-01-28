<?php


namespace Morebec\YDB\InMemory;

use Morebec\Collections\HashMap;
use Morebec\YDB\CollectionIndex;
use Morebec\YDB\Document;
use Morebec\YDB\DocumentCollectionInterface;

class InMemoryDocumentCollection implements DocumentCollectionInterface
{
    /**
     * @var HashMap
     */
    private $documents;

    /**
     * @var HashMap
     */
    private $indexes;

    public function __construct()
    {
        $this->documents = new HashMap();
        $this->indexes = new HashMap([
            Document::ID_FIELD => new CollectionIndex(Document::ID_FIELD)
        ]);
    }

    /**
     * @inheritDoc
     */
    public function insertOneDocument(Document $document): void
    {
        $this->insertDocuments([$document]);
    }

    /**
     * @inheritDoc
     */
    public function insertDocuments(array $documents)
    {
        foreach ($documents as $document) {
            $this->documents->put($document->getId(), $this->cloneDocument($document));
            $this->indexDocument($document);
        }
    }

    /**
     * @inheritDoc
     */
    public function updateOneDocument(Document $document): void
    {
        $this->updateDocuments([$document]);
    }

    /**
     * Updates multiple documents
     * @param array $documents
     */
    public function updateDocuments(array $documents): void
    {
        foreach ($documents as $document) {
            $this->documents->put($document->getId(), $this->cloneDocument($document));
        }
        $this->rebuildIndexes();
    }

    /**
     * @inheritDoc
     */
    public function removeOneDocument(Document $document): void
    {
        $this->removeDocuments([$document]);
    }

    /**
     * @inheritDoc
     */
    public function removeDocuments(array $documents): void
    {
        foreach ($documents as $document) {
            $this->documents->remove($document->getId());
        }
        $this->rebuildIndexes();
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
        return $this->indexes->containsKey($fieldName);
    }

    /**
     * Indexes a document
     * @param Document $document
     */
    private function indexDocument(Document $document): void
    {
        /** @var CollectionIndex $index */
        foreach ($this->indexes as $index) {
            $index->indexOneDocument($document);
        }
    }

    /**
     * Update all indexes
     */
    private function rebuildIndexes(): void
    {
        /** @var CollectionIndex $index */
        foreach ($this->indexes as $index) {
            $index->clear();
            $index->indexDocuments($this->getDocuments());
        }
    }

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        $this->documents->clear();
    }
}