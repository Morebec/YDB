<?php


namespace Morebec\YDB;


interface DocumentCollectionInterface
{
    /**
     * Inserts a document in this collection
     * @param Document $document
     */
    public function insertDocument(Document $document): void;

     /**
     * Updates a document in this collection
     * @param Document $document
     */
    public function updateDocument(Document $document): void;

    /**
     * Removes a document from this collection
     * @param Document $document
     */
    public function removeDocument(Document $document): void;

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
     * Cleats this collection, removing all records
     * @return void
     */
    public function clear(): void ;
}