<?php 

namespace Morebec\YDB\Contract;

use Morebec\ValueObjects\ValueObjectInterface;

/**
 * A criterion is used by queries to test multiple criteria and determine if a
 * record matches a query.
 * A criterion tests a single field against a value
 */
interface CriterionInterface extends ValueObjectInterface
{   
    /**
     * Indicates if a value matches this criterion     
     * @param  mixed $value the value to test
     * @return bool true if record matches, otherwise false
     */
    public function valueMatches($value): bool;

    /**
     * Indicates if it matches a record
     * @param  RecordInterface $record record
     * @return bool                  true if record matches, otherwise false
     */
    public function matchesRecord(RecordInterface $record): bool;

    /**
     * Indicates if the criterion supports a given field
     * @param  string $fieldName name of the field
     * @return bool              true if the field is supported, otherwise false
     */
    public function supportsField(string $fieldName): bool;
}
