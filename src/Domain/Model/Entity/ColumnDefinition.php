<?php


namespace Morebec\YDB\Domain\Model\Entity;


class ColumnDefinition
{
    /** @var string */
    private $name;

    /** @var ColumnType  */
    private $type;

    /** @var bool indicates if this column's values should be indexed */
    private $indexed;

    /** @var bool indicates if this column's values should be unique */
    private $unique;

    /** @var mixed the default value of this column's values when it is not specified */
    private $defaultValue;

    private function __construct(
        string $name,
        ColumnType $type,
        bool $indexed = false,
        bool $unique = false,
        $defaultValue = null
    )
    {
        $this->name = $name;
        $this->type = $type;
        $this->indexed = $indexed;
        $this->unique = $unique;
        $this->defaultValue = $defaultValue;
    }

    public static function create(
        string $name,
        ColumnType $type,
        bool $indexed = false,
        bool $unique = false,
        $defaultValue = null
    ): self
    {
        return new static($name, $type, $indexed, $unique, $defaultValue);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return ColumnType
     */
    public function getType(): ColumnType
    {
        return $this->type;
    }

    /**
     * @param ColumnType $type
     */
    public function setType(ColumnType $type): void
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isIndexed(): bool
    {
        return $this->indexed;
    }

    /**
     * @param bool $indexed
     */
    public function setIndexed(bool $indexed): void
    {
        $this->indexed = $indexed;
    }

    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }

    /**
     * @param bool $unique
     */
    public function setUnique(bool $unique): void
    {
        $this->unique = $unique;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     */
    public function setDefaultValue($defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }
}