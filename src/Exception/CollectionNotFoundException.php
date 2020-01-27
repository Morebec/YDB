<?php


namespace Morebec\YDB\Exception;


use Throwable;

class CollectionNotFoundException extends YDBException
{
    public function __construct(string $collectionName, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Collection '{$collectionName}' not found", $code, $previous);
    }
}