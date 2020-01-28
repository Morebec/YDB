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
    )
    {
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
     * at once
     * @return array
     */
    public function fetchAll(): array
    {
        return iterator_to_array($this->documentIterator);
    }

    /**
     * Returns the number of elements fetched.
     * Beware of performance, this will go through
     * the whole iterator items one by one.
     * NOTE: at the end of the count, the iterator is reset
     * to its first element.
     * @return int
     */
    public function getCount(): int
    {
        // Clone a generator so it can be rewinded
        $generator = $this->documentIterator;
        $documentIterator = static function ($e) use($generator) { yield $generator; };
        $count = iterator_count($this->documentIterator);
        $this->documentIterator = $documentIterator;

        return $count;
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
