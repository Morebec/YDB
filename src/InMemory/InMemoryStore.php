<?php


namespace Morebec\YDB\InMemory;

use Generator;
use Iterator;
use Morebec\Collections\HashMap;
use Morebec\YDB\CollectionIndex;
use Morebec\YDB\Document;
use Morebec\YDB\DocumentCollectionInterface;
use Morebec\YDB\DocumentStoreInterface;
use Morebec\YDB\Exception\DocumentCollectionAlreadyExistsException;
use Morebec\YDB\Exception\DocumentCollectionNotFoundException;
use Morebec\YDB\YQL\Cardinality;
use Morebec\YDB\YQL\PYQLQueryEvaluator;
use Morebec\YDB\YQL\Query\ExpressionQuery;
use Morebec\YDB\YQL\Query\QueryResult;

/**
 * Class InMemoryStore
 * In memory document repository
 */
class InMemoryStore implements DocumentStoreInterface
{
    /**
     * @var HashMap<string, InMemoryDocumentCollection> $collections
     */
    private $collections;

    public function __construct()
    {
        $this->collections = new HashMap();
    }

    /**
     * @inheritDoc
     */
    public function insertOne(string $collectionName, Document $document): void
    {
        $this->ensureCollectionExists($collectionName);

        /** @var DocumentCollectionInterface $collection */
        $collection = $this->collections->get($collectionName);
        $collection->insertOneDocument($document);
    }

    /**
     * @inheritDoc
     */
    public function insertMany(string $collectionName, array $documents): void
    {
        $this->ensureCollectionExists($collectionName);

        /** @var DocumentCollectionInterface $collection */
        $collection = $this->collections->get($collectionName);
        $collection->insertDocuments($documents);
    }

    /**
     * @inheritDoc
     */
    public function replaceOne(string $collectionName, Document $document): void
    {
        $this->ensureCollectionExists($collectionName);

        /** @var DocumentCollectionInterface $collection */
        $collection = $this->collections->get($collectionName);
        $collection->replaceOneDocument($document);
    }

    /**
     * @inheritDoc
     */
    public function replaceMany(string $collectionName, array $documents): void
    {
        $this->ensureCollectionExists($collectionName);

        /** @var DocumentCollectionInterface $collection */
        $collection = $this->collections->get($collectionName);
        $collection->replaceDocuments($documents);
    }

    /**
     * @inheritDoc
     */
    public function updateOne(ExpressionQuery $query, array $data): QueryResult
    {
        $collection = $this->getCollectionOrThrowException($query->getCollectionName());
        $result = $this->findBy($query);

        $document = $result->fetch();

        if ($document) {
            $collection->updateOneDocument($document, $data);
        }

        $f = static function () use ($document): Generator {
            yield $document;
        };
        $gen = $f();
        return new QueryResult($gen, $query);
    }

    /**
     * @inheritDoc
     */
    public function updateMany(ExpressionQuery $query, array $data): QueryResult
    {
        $collection = $this->getCollectionOrThrowException($query->getCollectionName());
        $result = $this->findBy($query);

        $documents = $result->fetchAll();

        $collection->updateDocuments($documents, $data);

        $f = static function () use ($documents): Generator {
            foreach ($documents as $d) {
                yield $d;
            }
        };
        $gen = $f();
        return new QueryResult($gen, $query);
    }

    /**
     * @inheritDoc
     */
    public function deleteMany(ExpressionQuery $query): QueryResult
    {
        $collection = $this->getCollectionOrThrowException($query->getCollectionName());
        $result = $this->findBy($query);

        $documents = $result->fetchAll();

        $collection->removeDocuments($documents);

        $f = static function () use ($documents): Generator {
            foreach ($documents as $d) {
                yield $d;
            }
        };
        $gen = $f();
        return new QueryResult($gen, $query);
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
    public function deleteOne(ExpressionQuery $query): QueryResult
    {
        $result = $this->findBy($query);

        $docs = $result->fetchAll();
        if (!empty($docs)) {
            /** @var DocumentCollectionInterface $collection */
            $collection = $this->collections->get($query->getCollectionName());
            $collection->removeDocuments([$docs[0]]);
        }

        $f = static function () use ($docs): Generator {
            foreach ($docs as $d) {
                yield $d;
                return;
            }
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
        $this->ensureCollectionNotExists($collectionName);
        $this->collections->put($collectionName, new InMemoryDocumentCollection());
    }

    /**
     * @inheritDoc
     */
    public function clearCollection(string $collectionName): void
    {
        $collection = $this->getCollectionOrThrowException($collectionName);
        $collection->clear();
    }

    /**
     * @inheritDoc
     */
    public function dropCollection(string $collectionName): void
    {
        $this->collections->remove($collectionName);
    }

    /**
     * @inheritDoc
     */
    public function renameCollection(string $collectionName, string $newName): void
    {
        $this->ensureCollectionExists($collectionName);
        $this->ensureCollectionNotExists($newName);

        $collection = $this->collections->get($collectionName);
        $this->dropCollection($collectionName);
        $this->collections->put($newName, $collection);
    }

    /**
     * Converts this store to an array for serialization
     * @return array
     */
    public function toArray(): array
    {
        $dump = [];
        $dump['collections'] = [];
        /** @var DocumentCollectionInterface $collection */
        foreach ($this->collections as $name => $collection) {
            $dump['collections'][$name] = [];
            $dump['collections'][$name]['documents'] = [];
            $dump['collections'][$name]['indexes'] = [];
            /** @var Document $document */
            foreach ($collection->getDocuments() as $document) {
                $dump['collections'][$name]['documents'][] = $document->toArray();
            }

            /** @var CollectionIndex $index */
            foreach ($collection->getIndexes() as $index) {
                $dump['collections'][$name]['indexes'][$index->getName()] = [
                    'order' => $index->getOrder(),
                    'field' => $index->getField(),
                    'values' => $index->getValues()
                ];
            }
        }

        return $dump;
    }

    /**
     * Throws a Collection Not found
     * @param string $collectionName
     * @throws DocumentCollectionNotFoundException
     */
    private function throwCollectionNotFoundException(string $collectionName): void
    {
        throw new DocumentCollectionNotFoundException($collectionName);
    }

    /**
     * Ensure a collection exists or throws an exception
     * @param string $collectionName
     * @throws DocumentCollectionNotFoundException
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
        // TODO use query planner
        foreach ($documents as $document) {
            if (PYQLQueryEvaluator::evaluateExpressionForDocument($query->getExpression(), $document)) {
                yield $document;
            }
            if ($query->getCardinality()->isEqualTo(Cardinality::ONE())) {
                return;
            }
        }
    }

    /**
     * @param string $newName
     * @throws DocumentCollectionAlreadyExistsException
     */
    private function ensureCollectionNotExists(string $newName): void
    {
        if ($this->collectionExists($newName)) {
            throw new DocumentCollectionAlreadyExistsException($newName);
        }
    }

    /**
     * @param string $collectionName
     * @return DocumentCollectionInterface
     * @throws DocumentCollectionNotFoundException
     */
    private function getCollectionOrThrowException(string $collectionName): DocumentCollectionInterface
    {
        $this->ensureCollectionExists($collectionName);

        /** @var DocumentCollectionInterface $collection */
        $collection = $this->collections->get($collectionName);
        return $collection;
    }
}
