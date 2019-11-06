<?php 

namespace Morebec\YDB;

/**
 * Helper class to create an IndexedColumn
 */
class IndexedColumn extends Column
{
    function __construct(string $name, ColumnType $type)
    {
        parent::__construct($name, $type, true);
    }
}

