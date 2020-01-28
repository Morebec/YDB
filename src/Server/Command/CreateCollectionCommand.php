<?php


namespace Morebec\YDB\Server\Command;

class CreateCollectionCommand
{
    public const NAME = 'create_collection';

    public $collectionName;

    public function __construct(string $collectionName)
    {
        $this->collectionName = $collectionName;
    }
}
