<?php 

namespace Morebec\YDB\Database;

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
    public function matches(RecordInterface $record): bool;

    /**
     * Returns the list of criteria used in this query
     * @return array
     */
    public function getCriteria(): array;
}