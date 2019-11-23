<?php

namespace Morebec\YDB\Contract;

use Morebec\ValueObjects\ValueObjectInterface;

/**
 * A Term is used by queries to test multiple criteria and determine if a
 * record matches a query.
 * A Term tests a single field against a value
 */
interface TermInterface extends ValueObjectInterface
{
    /**
     * Indicates if a value matches this Term
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
     * Indicates if the Term supports a given field
     * @param  string $fieldName name of the field
     * @return bool              true if the field is supported, otherwise false
     */
    public function supportsField(string $fieldName): bool;
}
