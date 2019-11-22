<?php 

namespace Morebec\YDB;

use Morebec\YDB\Contract\ColumnInterface;
use Morebec\YDB\Contract\ColumnTypeInterface;
use Morebec\YDB\Entity\Column;
use Morebec\YDB\Enum\ColumnType;

/**
 * ColumnBuilder
 */
class ColumnBuilder
{
    /** @var string name of the column */
    private $columnName;

    /** @var ColumnType type of the column */
    private $type;

    /** @var indicate if the column is indexed */
    private $indexed;

    /** @var bool indicates if the column is primary */
    private $primary;

    /** @var bool indicates if the column only has unique values */
    private $unique;


    function __construct(string $name)
    {
        $this->columnName = $name;
        $this->indexed = false;
        $this->unique = false;
        $this->primary = false;
    }

    /**
     * Sets the name of the column
     * @param  string $name name of the column
     * @return self
     */
    public static function withName(string $name): self
    {
        return new static($name);
    }

    /**
     * Sets the column type
     * @return self
     */
    public function withType(ColumnTypeInterface $type): self
    {
        $this->columnType = $type;

        return $this;
    }

    /**
     * Sets the column type to string
     * @return self
     */
    public function withStringType(): self
    {
        $this->withType(ColumnType::STRING());

        return $this;
    }

    /**
     * Sets the column type to integer
     * @return self
     */
    public function withIntegerType(): self
    {
        $this->withType(ColumnType::INTEGER());

        return $this;
    }

    /**
     * Sets the column type to flato
     * @return self
     */
    public function withFloatType(): self
    {
        $this->withType(ColumnType::FLOAT());

        return $this;
    }

    /**
     * Sets the column type to boolean
     * @return self
     */
    public function withBooleanType(): self
    {
        $this->withType(ColumnType::BOOLEAN());

        return $this;
    }

    /**
     * Sets the column type to array
     * @return self
     */
    public function withArrayType(): self
    {
        $this->withType(ColumnType::ARRAY());

        return $this;
    }

    /**
     * Make the column indexed
     * @return self
     */
    public function indexed(): self
    {
        $this->indexed = true;

        return $this;

    }

    /**
     * Make the column unique
     * @return self
     */
    public function unique(): self
    {
        $this->unique = true;

        return $this;
    }

    /**
     * Make the column primary
     * @return self
     */
    public function primary(): self
    {
        $this->primary = true;

        return $this;
    }

    /**
     * Builds the column object
     * @return self
     */
    public function build(): ColumnInterface
    {
        $indexed = $this->unique || $this->primary || $this->indexed;

        return new Column($this->columnName, $this->columnType, $indexed);
    }
}