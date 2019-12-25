<?php


namespace Morebec\YDB\Domain\Model\Entity;


use Assert\Assert;
use Prophecy\Exception\InvalidArgumentException;

class TableSchema
{
    /** @var string */
    private $tableName;

    /** @var ColumnDefinition[] */
    private $columns;

    /**
     * Constructs a TableSchema Object
     * @param string $tableName name of the table
     * @param ColumnDefinition[]  $columns   columns of the table
     */
    private function __construct(string $tableName, array $columns)
    {
        Assert::that($tableName)
            ->notBlank('The name of a table cannot be blank')
            ->notContains(' ', 'The name of a table cannot contain spaces')
            ->notRegex(
                '/[#$%^&*()+=\[\]\';,.\/{}|":<>?~\\\\]/',
                'The name of a table cannot contain special characters in (#$%^&*()+=\[\]\';,.\/{}|":<>?~\)'
            )
        ;

        // Make sure columns are unique
        $this->columns = [];
        foreach ($columns as $col) {
            $this->addColumn($col);
        }

        $this->tableName = $tableName;
    }

    /**
     * Creates a new schema instance
     * @param string $tableName
     * @param array $columns
     * @return static
     */
    public static function create(string $tableName, array $columns): self
    {
        return new static($tableName, $columns);
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return ColumnDefinition[]
     */
    public function getColumns(): array
    {
        return array_values($this->columns);
    }

    /**
     * Returns a column by its name or throw an exception if it does not exist
     * @param string $name
     * @return ColumnDefinition
     */
    public function getColumnByName(string $name): ColumnDefinition
    {
        foreach ($this->columns as $column) {
            if ($column->getName() === $name) {
                return $column;
            }
        }
        throw new \InvalidArgumentException("Column '{$name}' does not exist on table' {$this->tableName}'");
    }

    /**
     * Indicates if a column with a certain name exists
     * @param  string $columnName name of the column
     * @return bool
     */
    public function columnWithNameExists(string $columnName): bool
    {
        return array_key_exists($columnName, $this->columns);
    }

    /**
     * @param array $columns
     * @param ColumnDefinition $col
     * @param array $names
     */
    private function addColumn(ColumnDefinition $col): void
    {
        $columnName = $col->getName();
        if($this->columnWithNameExists($columnName))  {
            throw new InvalidArgumentException("Multiple definitions for column '{$columnName}'");
        }

        $this->columns[$columnName] = $col;
    }
}