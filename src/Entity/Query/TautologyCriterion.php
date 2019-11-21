<?php 

namespace Morebec\YDB\Entity\Query;

use Morebec\YDB\Contract\CriterionInterface;

/**
 * TautologyCriterion always match a record, it is used
 * so that a Query using this would find all the records
 */
class TautologyCriterion implements CriterionInterface
{
    function __construct()
    {
    }

    /**
     * Indicates if a value matches this criterion     
     * @param  mixed $value the value to test
     * @return bool true if record matches, otherwise false
     */
    public function valueMatches($value): bool
    {
        return true;
    }

    /**
     * Indicates if it matches a record
     * @param  RecordInterface $record record
     * @return bool                  true if record matches, otherwise false
     */
    public function matchesRecord(RecordInterface $record): bool
    {
        return true;
    }

    /**
     * Indicates if the criterion supports a given field
     * @param  string $fieldName name of the field
     * @return bool              true if the field is supported, otherwise false
     */
    public function supportsField(string $fieldName): bool
    {
        return true;
    }
}