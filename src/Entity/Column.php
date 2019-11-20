<?php 

namespace Morebec\YDB\Entity;

use Assert\Assert;
use Assert\Assertion;
use Morebec\ValueObjects\ValueObjectInterface;
use Morebec\YDB\Contract\ColumnInterface;
use Morebec\YDB\Contract\ColumnTypeInterface;

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

    /** @var mixed the default value for this column */
    private $defaultValue;

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
        
        $default = array_key_exists('default', $data) ? $data['default'] : null;

        return new static(
            $data['name'], 
            new ColumnType($data['type']), 
            $data['indexed'],
            $data['default']
        );
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
        bool $indexed = false,
        $defaultValue = null
    )
    {
        Assert::that($name)
            ->notBlank('The name of a column cannot be blank')
            ->notContains(' ', 'The name of a column cannot contain spaces')
            ->notRegex(
                '/[#$%^&*()+=\-\[\]\';,.\/{}|":<>?~\\\\]/', 
                'The name of a column cannot contain special characters'
            )
        ;

        $this->name = $name;
        $this->type = $type;
        $this->indexed = $indexed;
        $this->defaultValue = null;
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

    /**
     * @return bool
     */
    public function getIndexed(): bool
    {
        return $this->indexed;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
}