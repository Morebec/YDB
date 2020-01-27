<?php


namespace Morebec\YDB;


use Morebec\YDB\YQL\Query\ExpressionQuery;
use Morebec\YDB\YQL\Query\QueryResult;

interface YDBClientInterface
{
    /**
     * Inserts a document in a collection
     * @param string $collectionName
     * @param Document $document
     */
    public function insertDocument(string $collectionName, Document $document): void;

    /**
     * Updates a document in a collection
     * @param string $collectionName
     * @param Document $document
     */
    public function updateDocument(string $collectionName, Document $document): void;

    /**
     * Finds one document matching a given query or null if none found
     * @param ExpressionQuery $query
     * @return QueryResult
     */
    public function executeQuery(ExpressionQuery $query): QueryResult;

    /**
     * Deletes a document in a collection matching a given query
     * @param ExpressionQuery $query
     */
    public function deleteDocument(ExpressionQuery $query): void;

    /**
     * Creates a Collection
     * @param string $collectionName
     */
    public function createCollection(string $collectionName): void;

    /**
     * Drops a collection
     * @param string $collectionName
     */
    public function dropCollection(string  $collectionName): void;

    /**
     * Clears a collection
     * @param string $collectionName
     */
    public function clearCollection(string $collectionName): void;
}