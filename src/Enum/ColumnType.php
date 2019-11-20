<?php 

namespace Morebec\YDB\Enum;

use Morebec\ValueObjects\BasicEnum;
use Morebec\YDB\Contract\ColumnTypeInterface;

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
     * ColumnType::STRING() returning and instance with the provided value
     */
    public static function __callStatic($method, $arguments)
    {
        return new static(strtolower($method));
    }
}