<?php

namespace Morebec\YDB\YQL\Query;

use Iterator;
use Morebec\YDB\Document;

/**
 * QueryResult
 */
class QueryResult
{
    /** @var Iterator generator to the documents */
    private $documentIterator;

    /** @var ExpressionQuery */
    private $query;

    /** @var QueryStatistics */
    private $statistics;


    public function __construct(
        Iterator $documentIterator,
        ExpressionQuery $query,
        ?QueryStatistics $statistics = null
    ) {
        $this->documentIterator = $documentIterator;
        $this->query = $query;
        $this->statistics = $statistics;
    }

    /**
     * Retrieves the next Document found for the query.
     * Moves the pointer forward so subsequent calls
     * always return the next Document.
     * When there are no more documents, returns null
     * @return Document|null
     */
    public function fetch(): ?Document
    {
        $document = $this->documentIterator->current();
        $this->documentIterator->next();

        return $document !== false ? $document : null;
    }

    /**
     * Retrieves all the Documents found for the query
     * at once.
     * Note: Once this method has been executed, it is not possible to fetch the data again nor counting it
     * @return array
     */
    public function fetchAll(): array
    {
        $documents = iterator_to_array($this->documentIterator);

        return $documents;
    }

    /**
     * Returns the number of elements fetched.
     * Once this method has been executed, it is not possible to fetch the data again
     * to its first element.
     * @return int
     */
    public function getCount(): int
    {
        return iterator_count($this->documentIterator);
    }
    /**
     * Returns the Query associated with the result
     * @return ExpressionQuery
     */
    public function getQuery(): ExpressionQuery
    {
        return $this->query;
    }

    /**
     * Returns the duration of the query
     * @return int
     */
    public function getDuration(): int
    {
        return $this->statistics->getDuration();
    }

    /**
     * Returns the duration of the query planner step
     * @return int
     */
    public function getQueryPlannerDuration(): int
    {
        return $this->statistics->getQueryPlannerDuration();
    }
}
