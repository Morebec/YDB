<?php


namespace Morebec\YDB;

use InvalidArgumentException;
use Morebec\ValueObjects\Identity\UuidIdentifier;
use Morebec\ValueObjects\StringBasedValueObject;

class DocumentId extends StringBasedValueObject
{
    public function __construct(string $value)
    {
        if (!$value) {
            throw new InvalidArgumentException('A Document id cannot be blank');
        }
        parent::__construct($value);
    }

    /**
     * Creates a new if from a string
     * @param string $id
     * @return static
     */
    public static function fromString(string $id): self
    {
        return new static($id);
    }

    /**
     *
     * @return static
     */
    public static function generate(): self
    {
        return new static(UuidIdentifier::generate());
    }
}
