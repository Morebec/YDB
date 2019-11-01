<?php

namespace Morebec\YDB;

use Assert\Assertion;
use Morebec\ValueObjects\ValueObjectInterface;
use Morebec\YDB\Database\ColumnInterface;
use Morebec\YDB\Database\ColumnTypeInterface;

/**
 * Column
 */
class Column implements ColumnInterface
{
    /** @var string name of the column */
    private $name;

    /** @var ColumnTypeInterface type of the column */
    private $type;

    /** @var bool indicates if this column should be indexed or not */
    private $indexed;

    /**
     * Creates a column from an array representation
     * @param  array  $data data
     * @return Column
     */
    public static function fromArray(array $data): Column
    {
        Assertion::notEmpty($data);

        Assertion::keyExists($data, 'name');
        Assertion::keyExists($data, 'type');
        Assertion::keyExists($data, 'indexed');

        return new static($data['name'], new ColumnType($data['type']), $data['indexed']);
    }

    /**
     * Constructs an instance of Column
     * @param string              $name    name of the column
     * @param ColumnTypeInterface $type    type of the column
     * @param bool|boolean        $indexed indicates if it is indexed or not
     */
    function __construct(
        string $name, 
        ColumnTypeInterface $type, 
        bool $indexed = false
    )
    {
        // TODO Make column Type a ValueObject
        Assertion::notBlank($name);
        Assertion::notContains($name, ' ');
        $this->name = $name;

        $this->type = $type;

        $this->indexed = $indexed;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return ColumnTypeInterface
     */
    public function getType(): ColumnTypeInterface
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isIndexed(): bool
    {
        return $this->indexed;
    }

    /**
     * Indicates if this value object is equal to abother value object
     * @param  ValueObjectInterface $valueObject othervalue object to compare to
     * @return boolean                           true if equal otherwise false
     */
    public function isEqualTo(ValueObjectInterface $valueObject): bool
    {
        return (string)$this === (string)$valueObject;
    }

    /**
     * Returns a string representation of the value object
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->toArray());
    }

    /**
     * Returns an array representation of this column
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => (string)$this->type,
            'indexed' => $this->indexed
        ];
    }
}