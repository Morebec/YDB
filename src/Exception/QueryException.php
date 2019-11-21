<?php 

namespace Morebec\YDB\Exception;

use Morebec\YDB\Exception\DatabaseException;

/**
 * QueryException
 */
class QueryException extends DatabaseException
{
    
    function __construct(string $reason)
    {
        parent::__construct($reason);
    }
}