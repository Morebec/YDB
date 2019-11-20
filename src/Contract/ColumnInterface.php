<?php

namespace Morebec\YDB\Contract;

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