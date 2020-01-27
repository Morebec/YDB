<?php

namespace Morebec\YDB\YQL\Query;

use Morebec\ValueObjects\BasicEnum;

/**
 * @method static self EQUAL()
 * @method static self LOOSELY_EQUAL()
 * @method static self NOT_EQUAL()
 * @method static self LOOSELY_NOT_EQUAL()
 * @method static self LESS_THAN()
 * @method static self GREATER_THAN()
 * @method static self LESS_OR_EQUAL()
 * @method static self GREATER_OR_EQUAL()
 * @method static self IN()
 * @method static self NOT_IN()
 */
class TermOperator extends BasicEnum
{
    public const EQUAL = '===';
    public const LOOSELY_EQUALS = '==';

    public const NOT_EQUAL = '!==';
    public const LOOSELY_NOT_EQUAL = '!=';

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
     * TermOperator::EQUAL()
     */
    public static function __callStatic($method, $arguments): self
    {
        return new static(constant("self::$method"));
    }
}
