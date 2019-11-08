<?php 

namespace Morebec\YDB;

use Morebec\ValueObjects\BasicEnum;

/**
 * Criteria Operator
 */
class Operator extends BasicEnum
{
    const EQUAL = '==';
    const STRICTLY_EQUAL = '===';

    const NOT_EQUAL = '!==';
    const STRICTLY_NOT_EQUAL = '!=';

    const LESS_THAN = '<';
    const GREATER_THAN = '>';

    const LESS_OR_EQUAL = '<=';    
    const GREATER_OR_EQUAL = '>=';

    const IN = 'in';
    const NOT_IN = 'not_in';

    /** operator for arrays */
    const CONTAINS = 'contains';
    const NOT_CONTAINS = 'not_contains';

    public function __toString()
    {        
        return (string)$this->getValue();
    }

    /**
     * Used so it is poossible to do things like
     * Operator::US()
     */
    public static function __callStatic($method, $arguments)
    {

        return new static(constant("self::$method"));
    }
}
