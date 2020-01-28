<?php


namespace Morebec\YDB;

use InvalidArgumentException;

class CollectionIndex
{
    private const ORDER_ASCENDING = 1;
    private const ORDER_DESCENDING = -1;

    /** @var string name of the index */
    private $name;

    /** @var string field on which this index applies */
    private $field;

    /** @var mixed[] */
    private $values;

    /** @var int order of the index */
    private $order;

    /**
     * Index constructor.
     * If name is null, will generate an index name in the form 'index_field'
     * @param string $field
     * @param string|null $name
     * @param int $order
     */
    public function __construct(string $field, ?string $name = null, int $order = self::ORDER_ASCENDING)
    {
        if (!$field) {
            throw new InvalidArgumentException('Cannot create an index for field without a name');
        }
        $this->field = $field;

        if (!$name) {
            $orderStr = $order === self::ORDER_ASCENDING ? 'asc' : 'desc';
            $name = "index_{$field}_{$orderStr}";
        }
        $this->name = $name;
        $this->order = $order;
        $this->values = [];
    }

    /**
     * Clears this index
     */
    public function clear(): void
    {
        $this->values = [];
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @return mixed[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Indexes a document
     * @param Document $document
     */
    public function indexOneDocument(Document $document): void
    {
        $this->indexDocuments([$document]);
    }

    /**
     * Indexes a list of documents
     * @param Document[] $documents
     */
    public function indexDocuments(array $documents): void
    {
        /** @var Document $document */
        foreach ($documents as $document) {
            if (Document::ID_FIELD !== $this->field && !$document->hasField($this->field)) {
                continue;
            }

            $fieldValue = (string)($this->field === Document::ID_FIELD ? $document->getId() : $document[$this->field]);
            if (!array_key_exists($fieldValue, $this->values)) {
                $this->values[$fieldValue] = [];
            }

            $this->values[$fieldValue][] = (string)$document->getId();
        }

        // Update index
        $this->updateIndex();
    }

    /**
     * Updates the index by resorting all the values
     */
    private function updateIndex(): void
    {
        if ($this->order === self::ORDER_ASCENDING) {
            asort($this->values);
        } else {
            arsort($this->values);
        }
    }

    /**
     * Finds document ids for a given value
     * @param mixed $value
     * @return DocumentId[]
     */
    public function findDocumentIdForValue($value): array
    {
        return $this->values[(string)$value];
    }
}
