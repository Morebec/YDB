<?php


namespace Morebec\YDB\Domain\Model\Entity;

use Morebec\ValueObjects\BasicEnum;

/**
 * ColumnType
 * @method static BOOLEAN()
 * @method static ARRAY()
 * @method static FLOAT()
 * @method static INTEGER()
 * @method static STRING()
 */
class ColumnType extends BasicEnum
{
    const STRING = 'string';

    const BOOLEAN = 'boolean';

    const FLOAT = 'float';

    const INTEGER = 'integer';

    const ARRAY = 'array';

    /**
     * Used so it is possible to do things like
     * ColumnType::STRING() returning and instance with the provided value
     * @param $method
     * @param $arguments
     * @return ColumnType
     */
    public static function __callStatic($method, $arguments)
    {
        return new static(strtolower($method));
    }
}