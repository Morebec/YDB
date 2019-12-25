<?php


namespace Morebec\YDB\Domain\Model\Entity;


/**
 * Record
 */
class Record
{
    /** @var RecordId */
    private $id;

    /** @var mixed[] */
    private $data;

    private function __construct(RecordId $id, $data)
    {
        $this->id = $id;
        $this->data = $data;
    }

    /**
     * @param RecordId $id
     * @param $data
     * @return static
     */
    public static function create(RecordId $id, $data): self
    {
        return new static($id, $data);
    }

    /**
     * @return RecordId
     */
    public function getId(): RecordId
    {
        return $this->id;
    }

    /**
     * @param string $fieldName
     * @return mixed
     */
    public function getFieldValue(string $fieldName)
    {
        if ($fieldName === 'id') {
            return $this->getId();
        }

        if(!$this->hasField($fieldName)) {
            $fields = implode(',', $this->getFieldNames());
            throw new \InvalidArgumentException(
                "Field '{$fieldName}' not found in entity '{$this->getId()}' available fields are: {$fields}"
            );
        }

        return $this->data[$fieldName];
    }

    /**
     * Indicates if a field exists on this record
     * @param string $fieldName
     * @return bool
     */
    public function hasField(string $fieldName): bool
    {
        return array_key_exists($fieldName, $this->data);
    }

    /**
     * Returns the list of field names for this record
     * @return string[]
     */
    public function getFieldNames(): array
    {
        return array_keys($this->data);
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

    public function copy(): self
    {
        return clone $this;
    }

}