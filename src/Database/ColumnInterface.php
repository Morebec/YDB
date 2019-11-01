<?php

namespace Morebec\YDB\Database;

use Morebec\ValueObjects\ValueObjectInterface;

/**
 * ColumnInterface
 */
interface ColumnInterface extends ValueObjectInterface
{
    /**
     * Returns the name of the column
     * @return string
     */
    public function getName(): string;    
}