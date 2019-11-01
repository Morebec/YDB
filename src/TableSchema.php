<?php 

namespace Morebec\YDB;

use Assert\Assertion;
use Morebec\ValueObjects\ValueObjectInterface;
use Morebec\YDB\Database\ColumnInterface;
use Morebec\YDB\Database\TableSchemaInterface;

/**
 * TableSchema
 */
class TableSchema implements TableSchemaInterface
{
    const SCHEMA_FILE_NAME = 'schema.yaml';

    /** @var string name of the table */
    private $tableName;

    /**
     * Array Of Column Objects
     * @var array
     */
    private $columns;

    /**
     * Creates an TableSchema from an array
     * @param  array  $data data
     * @return TableSchemaInterface
     */
    public static function fromArray(array $data): TableSchemaInterface
    {
        $columns = [];

        Assertion::keyExists($data, 'columns');
        foreach ($data['columns'] as $col) {
            $columns[] = Column::fromArray($col);
        }

        Assertion::keyExists($data, 'table_name');
        return new TableSchema($data['table_name'], $columns);
    }


    function __construct(string $tableName, array $columns)
    {
        Assertion::notBlank($tableName);
        Assertion::notContains($tableName, ' ');

        $this->tableName = $tableName;
        $this->columns = $columns;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Returns a column by its name or null if it was not found
     * @param  string $name name of the potential column
     * @return ColumnInterface|null
     */
    public function getColumnByName(string $name): ?ColumnInterface
    {
        foreach ($this->columns as $column) {
            if($column->getName() === $name) {
                return $column;
            }
        }

        return null;
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
     * Returns an array representation of this table schema
     * @return array
     */
    public function toArray(): array
    {
        return [
            'table_name' => $this->tableName,
            'columns' => array_map(static function ($col) {
                return $col->toArray();
            }, $this->columns)
        ];    
    }

    /**
     * Indicates if a column with a certain name exists
     * @param  string $columnName name of the column
     * @return bool
     */
    public function columnWithNameExists(string $columnName): bool
    {
        return $this->getColumnByName($columnName) !== null;
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
}