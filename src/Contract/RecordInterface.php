<?php

namespace Morebec\YDB\Contract;

use Morebec\ValueObjects\ValueObjectInterface;

/**
 * Interface for Database Records
 */
interface RecordInterface
{
    /**
     * Returns theid of the record
     * @return RecordIdInterface
     */
    public function getId(): RecordIdInterface;

    /**
     * Returns the value of a field
     * @param  string $fieldName name of the field
     * @return mixed
     */
    public function getFieldValue(string $fieldName);

    /**
     * Sets a field's value
     * @param string $fieldName name of the field
     * @param mixed $value     new value of the field
     */
    public function setFieldValue(string $fieldName, $value): void;
    
    /**
     * Removes a field from a record
     * @param  string $fieldName field name
     * @return void
     */
    public function removeField(string $fieldName): void;

    /**
     * Indicates if a record has a specific field or not
     * @param  string  $fieldName name of the field
     * @return boolean            true if field exists, otherwise false
     */
    public function hasField(string $fieldName): bool;

    /**
     * Converts this record to an array
     * @return array
     */
    public function toArray(): array;

    /**
     * Creates a copy of the record
     * @return RecordInterface
     */
    public function copy(): RecordInterface;
}
