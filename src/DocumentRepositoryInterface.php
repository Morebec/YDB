<?php


namespace Morebec\YDB;



use Morebec\YDB\YQL\Query\ExpressionQuery;
use Morebec\YDB\YQL\Query\QueryResult;

/**
 * Interface DocumentRepositoryInterface
 */
interface DocumentRepositoryInterface
{
    /**
     * Adds a document to this repository
     * @param string $collectionName
     * @param Document $document
     * @return mixed
     */
    public function add(string $collectionName, Document $document);

    /**
     * Updates a document in this repository
     * @param string $collectionName
     * @param Document $document
     * @return mixed
     */
    public function update(string $collectionName, Document $document);

    /**
     * Finds one document in this repository matching a given query
     * @param ExpressionQuery $query
     * @return mixed
     */
    public function findOneBy(ExpressionQuery $query);

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