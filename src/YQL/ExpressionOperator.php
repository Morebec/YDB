<?php

namespace Morebec\YDB\YQL;

use Morebec\ValueObjects\BasicEnum;

/**
 * ExpressionOperator
 * @method static self AND ()
 * @method static self OR ()
 */
class ExpressionOperator extends BasicEnum
{
    public const AND = 'AND';
    public const OR = 'OR';

    public static function __callStatic($method, $arguments)
    {
        return new static($method);
    }
}
