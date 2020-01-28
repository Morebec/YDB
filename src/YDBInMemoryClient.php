<?php


namespace Morebec\YDB;

use Morebec\YDB\InMemory\InMemoryRepository;
use Morebec\YDB\YQL\Query;
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
    public function updateOneDocument(string $collectionName, Document $document): void
    {
        $this->repository->update($collectionName, $document);
    }

    /**
     * @inheritDoc
     */
    public function updateDocuments(string $collectionName, array $documents): void
    {
        $this->repository->updateMany($collectionName, $documents);
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
    public function deleteDocument(ExpressionQuery $query): QueryResult
    {
        return $this->repository->remove($query);
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
        $this->repository->dropCollection($collectionName);
    }

    /**
     * @inheritDoc
     */
    public function clearCollection(string $collectionName): void
    {
        $this->repository->clearCollection($collectionName);
    }
}