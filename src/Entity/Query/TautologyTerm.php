<?php

namespace Morebec\YDB\Entity\Query;

use Morebec\ValueObjects\ValueObjectInterface;
use Morebec\YDB\Contract\RecordInterface;
use Morebec\YDB\Contract\TermInterface;

/**
 * TautologyTerm always match a record, it is used
 * so that a Query using this would find all the records
 */
class TautologyTerm implements TermInterface
{
    public function __construct()
    {
    }

    /**
     * Indicates if a value matches this Term
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
     * Indicates if the Term supports a given field
     * @param  string $fieldName name of the field
     * @return bool              true if the field is supported, otherwise false
     */
    public function supportsField(string $fieldName): bool
    {
        return true;
    }

    public function __toString()
    {
        return 'true === true';
    }

    public function isEqualTo(ValueObjectInterface $vo): bool
    {
        return (string)$this === (string)$vo;
    }
}
