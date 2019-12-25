<?php

namespace Morebec\YDB\Domain\YQL\Query;

use Morebec\YDB\Domain\Model\Entity\Record;

/**
 * QueryResult
 */
class QueryResult
{
    /** @var \Iterator generator the to records */
    private $recordIterator;

    /** @var Query */
    private $query;

    /** @var QueryStatistics */
    private $statistics;


    public function __construct(
        \Iterator $recordIterator, 
        Query $query,
        QueryStatistics $statistics
    )
    {
        $this->recordIterator = $recordIterator;
        $this->query = $query;
        $this->statistics = $statistics;
    }

    /**
     * Retreives the next Record found for the query.
     * Moves the pointer forward so subsequent calls
     * return always return the next Record.
     * When there are no more records, returns null
     * @return Record|null
     */
    public function fetch(): ?Record
    {
        $record = $this->recordIterator->current();
        $this->recordIterator->next();

        return $record !== false ? $record : null;
    }

    /**
     * Retrieves all the Records found for the query
     * @return array
     */
    public function fetchAll(): array
    {
        return iterator_to_array($this->recordIterator);
    }

    /**
     * Returns the Query associated with the result
     * @return Query
     */
    public function getQuery(): Query
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
