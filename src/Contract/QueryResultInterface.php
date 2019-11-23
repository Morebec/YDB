<?php

namespace Morebec\YDB\Contract;

/**
 * Represents the result of a query
 */
interface QueryResultInterface
{
    /**
     * Retreives the next Record found for the query.
     * Moves the pointer forward so subsequent calls
     * return always return the next Record.
     * When there are no more records, returns null
     * @return RecordInterface|null
     */
    public function fetch(): ?RecordInterface;

    /**
     * Retrieves all the Records found for the query
     * @return array
     */
    public function fetchAll(): array;

    /**
     * Returns the Query associated with the result
     * @return QueryInterface
     */
    public function getQuery(): QueryInterface;
}
