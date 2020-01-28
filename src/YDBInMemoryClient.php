<?php


namespace Morebec\YDB;

use Morebec\YDB\InMemory\InMemoryRepository;
use Morebec\YDB\YQL\Query\ExpressionQuery;
use Morebec\YDB\YQL\Query\QueryResult;

class YDBInMemoryClient implements YDBClientInterface
{
    /**
     * @var InMemoryRepository
     */
    private $repository;

    public function __construct()
    {
        $this->repository = new InMemoryRepository();
    }

    /**
     * @inheritDoc
     */
    public function insertDocument(string $collectionName, Document $document): void
    {
        $this->repository->add($collectionName, $document);
    }

    /**
     * @inheritDoc
     */
    public function updateDocument(string $collectionName, Document $document): void
    {
        $this->repository->update($collectionName, $document);
    }

    /**
     * @inheritDoc
     */
    public function executeQuery(ExpressionQuery $query): QueryResult
    {
        return $this->repository->findBy($query);
    }

    /**
     * @inheritDoc
     */
    public function deleteDocument(ExpressionQuery $query): void
    {
        $this->repository->remove($query);
    }

    /**
     * @inheritDoc
     */
    public function createCollection(string $collectionName): void
    {
        $this->repository->createCollection($collectionName);
    }

    /**
     * @inheritDoc
     */
    public function dropCollection(string $collectionName): void
    {
        // TODO: Implement dropCollection() method.
    }

    /**
     * @inheritDoc
     */
    public function clearCollection(string $collectionName): void
    {
        // TODO: Implement clearCollection() method.
    }
}