<?php


namespace Morebec\YDB\InMemory;

use Morebec\Collections\HashMap;
use Morebec\YDB\Document;

class InMemoryDocumentCollection
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
     * Inserts a document in this collection
     * @param Document $document
     */
    public function insertDocument(Document $document): void
    {
        $this->documents->put($document->getId(), $this->cloneDocument($document));
    }

    /**
     * Updates a document in this collection
     * @param Document $document
     */
    public function updateDocument(Document $document): void
    {
        $this->documents->put($document->getId(), $this->cloneDocument($document));
    }

    /**
     * Removes a document from this collection
     * @param Document $document
     */
    public function removeDocument(Document $document): void
    {
        $this->documents->remove($document->getId());
    }

    /**
     * Returns all documents
     * @return array
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
}