<?php


namespace Morebec\YDB;



use Morebec\YDB\YQL\Query\ExpressionQuery;
use Morebec\YDB\YQL\Query\QueryResult;

/**
 * Interface DocumentStoreInterface
 */
interface DocumentStoreInterface
{
    /**
     * Adds a document to this repository
     * @param string $collectionName
     * @param Document $document
     * @return mixed
     */
    public function add(string $collectionName, Document $document): void;

    /**
     * Adds multiple documents to this repository
     * @param string $collectionName
     * @param array $documents
     */
    public function addMany(string $collectionName, array $documents): void;

    /**
     * Updates a document in this repository
     * @param string $collectionName
     * @param Document $document
     * @return mixed
     */
    public function update(string $collectionName, Document $document): void;

    /**
     * Updates multiple documents
     * @param string $collectionName
     * @param Document[] $documents
     */
    public function updateMany(string $collectionName, array $documents): void;

    /**
     * Finds all documents in this repository matching a given query
     * @param ExpressionQuery $query
     * @return mixed
     */
    public function findBy(ExpressionQuery $query): QueryResult;

    /**
     * Removes a document matching a given query. Returns a QueryResult with all the documents removed
     * @param ExpressionQuery $query
     * @return QueryResult
     */
    public function remove(ExpressionQuery $query): QueryResult;


    /**
     * Creates a collection
     * @param string $collectionName
     */
    public function createCollection(string $collectionName): void;

    /**
     * Clears a collection
     * @param string $collectionName
     */
    public function clearCollection(string $collectionName): void;
}