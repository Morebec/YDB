<?php

namespace Morebec\YDB\YQL;

use Morebec\ValueObjects\BasicEnum;

/**
 * ExpressionOperator
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
