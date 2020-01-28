<?php


namespace Morebec\YDB;

interface DocumentCollectionInterface
{
    /**
     * Inserts a document in this collection
     * @param Document $document
     */
    public function insertOneDocument(Document $document): void;

    /**
     * Inserts multiple documents
     * @param array $documents
     * @return mixed
     */
    public function insertDocuments(array $documents);

    /**
    * Replaces a document in this collection
    * @param Document $document
    */
    public function replaceOneDocument(Document $document): void;

    /**
     * Replaces multiple documents ib this collection
     * @param array $documents
     */
    public function replaceDocuments(array $documents): void;

    /**
     * Updates a single document in this collection
     * @param Document $document
     * @param array $data
     */
    public function updateOneDocument(Document $document, array $data): void;

    /**
     * Updates multiple documents in this collection
     * @param Document[] $documents
     * @param array $data
     */
    public function updateDocuments(array $documents, array $data): void;

    /**
     * Removes a document from this collection
     * @param Document $document
     */
    public function removeOneDocument(Document $document): void;

    /**
     * Removes all given documents from this collection
     * @param array $documents
     */
    public function removeDocuments(array $documents);

    /**
    * Returns all documents
    * @return array
    */
    public function getDocuments(): array;

    /**
     * Indicates if this collection has an index on a field
     * @param string $fieldName
     * @return bool
     */
    public function hasIndexOnField(string $fieldName): bool;

    /**
     * Returns all indexes of this collection
     * @return array
     */
    public function getIndexes(): array;

    /**
     * Cleats this collection, removing all records
     * @return void
     */
    public function clear(): void ;
}
