<?php

namespace Morebec\YDB;

use Assert\Assertion;
use Morebec\ValueObjects\Identity\UuidIdentifier;
use Morebec\ValueObjects\StringBasedValueObject;
use Morebec\YDB\Database\RecordIdInterface;

/**
 * RecordId
 */
class RecordId extends StringBasedValueObject implements RecordIdInterface
{
    function __construct(string $identifier)
    {
        Assertion::notBlank($identifier, 'A record id cannot be blank.');
        parent::__construct($identifier); 
    }

    public static function generate(): RecordId
    {
        return new static(UuidIdentifier::generate());
    }
}
