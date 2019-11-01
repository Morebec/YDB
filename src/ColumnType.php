<?php 

namespace Morebec\YDB;

use Morebec\ValueObjects\BasicEnum;
use Morebec\YDB\Database\ColumnTypeInterface;

/**
 * ColumnType
 */
class ColumnType extends BasicEnum implements ColumnTypeInterface
{
    const STRING = 'string';

    const BOOLEAN = 'boolean';

    const FLOAT = 'float';
    
    const INTEGER = 'integer';

    const ARRAY = 'array';

    /**
     * Used so it is poossible to do things like
     * Operator::US()
     */
    public static function __callStatic($method, $arguments)
    {
        return new static(strtolower($method));
    }
}
