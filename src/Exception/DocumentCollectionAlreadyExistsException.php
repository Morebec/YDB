<?php


namespace Morebec\YDB\Exception;

use Throwable;

/**
 * Thrown when an exception was expected not to exists
 */
class DocumentCollectionAlreadyExistsException extends YDBException
{
    public function __construct(string $collectionName, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("A collection with name {$collectionName} already exists", $code, $previous);
    }
}