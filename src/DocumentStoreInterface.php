<?php


namespace Morebec\YDB;

use Morebec\YDB\Exception\DocumentCollectionAlreadyExistsException;
use Morebec\YDB\Exception\DocumentCollectionNotFoundException;
use Morebec\YDB\Exception\QueryException;
use Morebec\YDB\YQL\Query\ExpressionQuery;
use Morebec\YDB\YQL\Query\QueryResult;

/**
 * Interface DocumentStoreInterface
 */
interface DocumentStoreInterface
{
    /**
     * Inserts a single document into this store
     * @param string $collectionName
     * @param Document $document
     * @throws DocumentCollectionNotFoundException
     */
    public function insertOne(string $collectionName, Document $document): void;

    /**
     * Inserts multiple documents into this store
     * @param string $collectionName
     * @param Document[] $documents
     * @throws DocumentCollectionNotFoundException
     */
    public function insertMany(string $collectionName, array $documents): void;

    /**
     * Replaces the entire content of a document in this store by overwriting it
     * @param string $collectionName
     * @param Document $document
     * @throws DocumentCollectionNotFoundException
     */
    public function replaceOne(string $collectionName, Document $document): void;

    /**
     * Replaces the contents of multiple documents by overwriting them
     * @param string $collectionName
     * @param Document[] $documents
     * @throws DocumentCollectionNotFoundException
     */
    public function replaceMany(string $collectionName, array $documents): void;

    /**
     * Updates the first document matching a query but applying the provided data.
     * If a field in the data does not exists for the matched document, it will be created with the value provided.
     * Returns the updated documents
     * @param ExpressionQuery $query
     * @param array $data
     * @return QueryResult
     * @throws DocumentCollectionNotFoundException
     * @throws QueryException
     */
    public function updateOne(ExpressionQuery $query, array $data): QueryResult;

    /**
     * Updates all documents matching a query by applying the provided data.
     * If a field in the data does not exists for the matched document, it will be created with the value provided.
     * Returns the updated documents
     * @param ExpressionQuery $query
     * @param array $data
     * @return QueryResult
     * @throws DocumentCollectionNotFoundException
     * @throws QueryException
     */
    public function updateMany(ExpressionQuery $query, array $data): QueryResult;

    /**
     * Finds all documents in this store matching a given query
     * @param ExpressionQuery $query
     * @return QueryResult
     * @throws DocumentCollectionNotFoundException
     * @throws QueryException
     */
    public function findBy(ExpressionQuery $query): QueryResult;

    /**
     * Deletes the first document matching a given query.
     * Returns a QueryResult with the document removed
     * @param ExpressionQuery $query
     * @return QueryResult
     * @throws DocumentCollectionNotFoundException
     * @throws QueryException
     */
    public function deleteOne(ExpressionQuery $query): QueryResult;

    /**
     * Deletes multiple documents from this store matching a query.
     * Returns a QueryResult with all the documents removed.
     * @param ExpressionQuery $query
     * @return QueryResult
     * @throws DocumentCollectionNotFoundException
     * @throws QueryException
     */
    public function deleteMany(ExpressionQuery $query): QueryResult;

    /**
     * Creates a collection
     * @param string $collectionName
     * @throws DocumentCollectionAlreadyExistsException
     */
    public function createCollection(string $collectionName): void;

    /**
     * Clears a collection
     * @param string $collectionName
     * @throws DocumentCollectionNotFoundException
     * @throws QueryException
     */
    public function clearCollection(string $collectionName): void;

    /**
     * Renames a collection
     * @param string $collectionName
     * @param string $newName
     * @throws DocumentCollectionNotFoundException
     * @throws DocumentCollectionAlreadyExistsException
     */
    public function renameCollection(string $collectionName, string $newName): void;

    /**
     * Drops a collection from this repository.
     * Does not throw an exception if it is not found
     * @param string $collectionName
     */
    public function dropCollection(string $collectionName): void;
}
