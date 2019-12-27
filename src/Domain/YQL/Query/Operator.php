<?php

namespace Morebec\YDB\Domain\YQL\Query;

use Morebec\ValueObjects\BasicEnum;

/**
 * Criteria Operator
 * @method static EQUAL()
 * @method static STRICTLY_EQUAL()
 * @method static NOT_EQUAL()
 * @method static STRICTLY_NOT_EQUAL()
 * @method static LESS_THAN()
 * @method static GREATER_THAN()
 * @method static LESS_OR_EQUAL()
 * @method static GREATER_OR_EQUAL()
 * @method static IN()
 * @method static NOT_IN()
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
