<?php


namespace Morebec\YDB;


use Morebec\Collections\HashMap;

class Document implements \ArrayAccess
{
    public const ID_FIELD = '_id';

    /** @var HashMap<string, mixed> */
    private $data;

    /**
     * @var DocumentId internal YDB id
     */
    private $_id;

    public function __construct(DocumentId $id, array $data = [])
    {
        $this->_id = $id;

        $this->data = new HashMap($data);
    }

    /**
     * Creates a new document with the given data
     * @param array $data
     * @return static
     */
    public static function create(array $data): self
    {
        return new static(DocumentId::generate(), $data);
    }

    /**
     * @return DocumentId
     */
    public function getId(): DocumentId
    {
        return $this->_id;
    }

    /**
     * Transforms this document to an array
     * @return array
     */
    public function toArray(): array
    {
        $data = $this->getData();
        $data[self::ID_FIELD] = (string)$this->getId();
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return $this->data->containsKey($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->data->get($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->data->put($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        $this->data->remove($offset);
    }

    /**
     * Indicates if a field exist on this document
     * @param string $field
     * @return bool
     */
    public function hasField(string $field): bool
    {
        return $this->data->containsKey($field);
    }

    /**
     * Replaces the data of this document
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = new HashMap($data);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data->toArray();
    }
}