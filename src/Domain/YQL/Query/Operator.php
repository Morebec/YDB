<?php

namespace Morebec\YDB\Domain\YQL\Query;

use Morebec\ValueObjects\BasicEnum;

/**
 * Criteria Operator
 * @method static EQUAL()
 */
class Operator extends BasicEnum
{
    public const EQUAL = '==';
    public const STRICTLY_EQUAL = '===';

    public const NOT_EQUAL = '!==';
    public const STRICTLY_NOT_EQUAL = '!=';

    public const LESS_THAN = '<';
    public const GREATER_THAN = '>';

    public const LESS_OR_EQUAL = '<=';
    public const GREATER_OR_EQUAL = '>=';

    public const IN = 'in';
    public const NOT_IN = 'not_in';

    /** operator for arrays */
    public const CONTAINS = 'contains';
    public const NOT_CONTAINS = 'not_contains';

    public function __toString()
    {
        return (string)$this->getValue();
    }

    /**
     * Used so it is possible to do things like
     * Operator::EQUAL()
     */
    public static function __callStatic($method, $arguments)
    {
        return new static(constant("self::$method"));
    }
}
