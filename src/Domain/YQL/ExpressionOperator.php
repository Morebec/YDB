<?php

namespace Morebec\YDB\Domain\YQL;

use Morebec\ValueObjects\BasicEnum;

/**
 * ExpressionOperator
 * @method static AND ()
 * @method static OR ()
 */
class ExpressionOperator extends BasicEnum
{
    const AND = 'AND';
    const OR = 'OR';

    public static function __callStatic($method, $arguments)
    {
        return new static($method);
    }
}