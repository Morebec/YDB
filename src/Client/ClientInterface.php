<?php


namespace Morebec\YDB\Client;

use Morebec\YDB\Document;
use Morebec\YDB\YQL\Query\ExpressionQuery;
use Morebec\YDB\YQL\Query\QueryResult;

interface ClientInterface
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
    public function updateOneDocument(string $collectionName, Document $document): void;

    /**
     * Updates multiple documents
     * @param string $collectionName
     * @param Document[] $documents
     */
    public function updateDocuments(string $collectionName, array $documents): void;

    /**
     * Finds one document matching a given query or null if none found
     * @param ExpressionQuery $query
     * @return QueryResult
     */
    public function executeQuery(ExpressionQuery $query): QueryResult;

    /**
     * Deletes a document in a collection matching a given query and returns the deleted documents
     * @param ExpressionQuery $query
     * @return QueryResult
     */
    public function deleteDocument(ExpressionQuery $query): QueryResult;

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
