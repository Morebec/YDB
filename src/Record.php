<?php 

namespace Morebec\YDB;

use Assert\Assertion;
use Morebec\ValueObjects\ValueObjectInterface;
use Morebec\YDB\Database\RecordIdInterface;
use Morebec\YDB\Database\RecordInterface;

/**
 * Record
 */
class Record implements RecordInterface
{
    /** @var RecrodIdInterface */
    private $id;

    /** @var array */
    private $data;

    function __construct(RecordIdInterface $id, array $data)
    {
        $this->id = $id;
        $this->data = $data;
    }

    /**
     * Returns the value of a field in the record
     * @param  string $fieldName name of the field
     * @return mixed
     */
    public function getFieldValue(string $fieldName)
    {
        if($fieldName === 'id') {
            return $this->getId();
        }

        Assertion::keyExists($this->data, $fieldName, 
            sprintf("Field '%s' not found in entity '%s' available fields are: %s",
                $fieldName,
                $this->getId(),
                join(',', array_keys($this->data))
            )
        );

        return $this->data[$fieldName];
    }

    /**
     * Sets a field's value
     * @param string $fieldName name of the field
     * @param mixed $value     new value of the field
     */
    public function setFieldValue(string $fieldName, $value): void
    {
        $this->data[$fieldName] = $value;
    }

    /**
     * Removes a field from a record
     * @param  string $fieldName record
     * @return void
     */
    public function removeField(string $fieldName): void
    {
        unset($this->data[$fieldName]);
    }

    /**
     * Indicates if a record has a specific field or not
     * @param  string  $fieldName name of the field
     * @return boolean            true if field exists, otherwise false
     */
    public function hasField(string $fieldName): bool
    {
        return array_key_exists($fieldName, $this->data);
    }

    /**
     * Returns theid of the record
     * @return RecordIdInterface
     */
    public function getId(): RecordIdInterface
    {
        return $this->id;
    }

    /**
     * Converts this record to an array
     * @return array
     */
    public function toArray(): array
    {
        $data = $this->data;
        $data['id'] = (string)$this->id;

        return $data;
    }

    /**
     * Creates a copy of the record
     * @return RecordInterface
     */
    public function copy(): RecordInterface
    {
        $arr = $this->toArray();

        return new static(
            new RecordId($arr['id']),
            $arr
        );
    }

    /**
     * Indicates if this record is equal to abother record
     * @param  RecordInterface $record othervalue object to compare to
     * @return boolean                           true if equal otherwise false
     */
    public function isEqualTo(RecordInterface $record): bool
    {
        return (string)$this == (string)$record;
    }

    /**
     * Returns a string representation of the value object
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->id;
    }
}