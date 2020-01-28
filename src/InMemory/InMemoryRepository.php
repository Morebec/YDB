<?php


namespace Morebec\YDB\InMemory;


use Iterator;
use Morebec\Collections\HashMap;
use Morebec\YDB\Document;
use Morebec\YDB\DocumentCollectionInterface;
use Morebec\YDB\DocumentRepositoryInterface;
use Morebec\YDB\Exception\CollectionNotFoundException;
use Morebec\YDB\YQL\Cardinality;
use Morebec\YDB\YQL\PYQLQueryEvaluator;
use Morebec\YDB\YQL\Query\ExpressionQuery;
use Morebec\YDB\YQL\Query\QueryResult;

/**
 * Class InMemoryRepository
 * In memory document repository
 */
class InMemoryRepository implements DocumentRepositoryInterface
{
    /**
     * @var HashMap<string, InMemoryDocumentCollection> $collections
     */
    private $collections;
    /**
     * @var PYQLQueryEvaluator
     */
    private $queryEvaluator;

    public function __construct()
    {
        $this->collections = new HashMap();
        $this->queryEvaluator = new PYQLQueryEvaluator();
    }

    /**
     * @inheritDoc
     */
    public function add(string $collectionName, Document $document)
    {
        $this->ensureCollectionExists($collectionName);

        /** @var DocumentCollectionInterface $collection */
        $collection = $this->collections->get($collectionName);
        $collection->insertDocument($document);
    }

    /**
     * @inheritDoc
     */
    public function update(string $collectionName, Document $document)
    {
        $this->ensureCollectionExists($collectionName);

        /** @var DocumentCollectionInterface $collection */
        $collection = $this->collections->get($collectionName);
        $collection->updateOneDocument($document);
    }

    /**
     * @inheritDoc
     */
    public function findBy(ExpressionQuery $query): QueryResult
    {
        $collectionName = $query->getCollectionName();
        $this->ensureCollectionExists($collectionName);

        /** @var InMemoryDocumentCollection $collection */
        $collection = $this->collections->get($collectionName);
        $iterator = $this->evaluateQueryForCollection($query, $collection);

        return new QueryResult($iterator, $query);

    }

    /**
     * @inheritDoc
     */
    public function remove(ExpressionQuery $query): QueryResult
    {
        $result = $this->findBy($query);

        $all = $result->fetchAll();

        /** @var DocumentCollectionInterface $collection */
        $collection = $this->collections->get($query->getCollectionName());

        $collection->removeDocuments($all);

        $f = static function() use ($all): \Generator {
            foreach ($all as $d) yield $d;
        };
        $gen = $f();
        return new QueryResult($gen, $query);
    }

    /**
     * Indicates if a collection exists in this repository
     * @param string $collectionName
     * @return bool
     */
    private function collectionExists(string $collectionName): bool
    {
        return $this->collections->containsKey($collectionName);
    }

    /**
     * @inheritDoc
     */
    public function createCollection(string $collectionName): void
    {
        $this->collections->put($collectionName, new InMemoryDocumentCollection());
    }

    /**
     * Throws a Collection Not found
     * @param string $collectionName
     * @throws CollectionNotFoundException
     */
    private function throwCollectionNotFoundException(string $collectionName): void
    {
        throw new CollectionNotFoundException($collectionName);
    }

    /**
     * Ensure a collection exists or throws an exception
     * @param string $collectionName
     * @throws CollectionNotFoundException
     */
    private function ensureCollectionExists(string $collectionName): void
    {
        if (!$this->collectionExists($collectionName)) {
            $this->throwCollectionNotFoundException($collectionName);
        }
    }

    /**
     * @param ExpressionQuery $query
     * @param InMemoryDocumentCollection $collection
     * @return Iterator
     */
    private function evaluateQueryForCollection(ExpressionQuery $query, InMemoryDocumentCollection $collection): Iterator
    {
        // Check cardinality
        $documents = $collection->getDocuments();
        foreach ($documents as $document) {
            if (PYQLQueryEvaluator::evaluateExpressionForDocument($query->getExpression(), $document)) {
                yield $document;
            }
            if($query->getCardinality()->isEqualTo(Cardinality::ONE())) {
                return;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function clearCollection(string $collectionName): void
    {
        $this->ensureCollectionExists($collectionName);

        /** @var DocumentCollectionInterface $collection */
        $collection = $this->collections->get($collectionName);
        $collection->clear();
    }
}