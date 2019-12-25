<?php


namespace Morebec\YDB\Domain\Model\Entity;


use Morebec\ValueObjects\Identity\UuidIdentifier;
use Morebec\ValueObjects\StringBasedValueObject;

class RecordId extends StringBasedValueObject
{
    public static function generate(): self
    {
        $id = UuidIdentifier::generate();
        return new static($id);
    }
}