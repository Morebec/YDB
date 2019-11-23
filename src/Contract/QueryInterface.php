<?php

namespace Morebec\YDB\Contract;

use Morebec\ValueObjects\ValueObjectInterface;

/**
 * Interface for Database Queries
 */
interface QueryInterface extends ValueObjectInterface
{
    /**
     * Indicates if a record matches this query
     * @param  RecordInterface $r query
     * @return bool             true if it matches otherwise false
     */
    public function matchesRecord(RecordInterface $record): bool;
}
