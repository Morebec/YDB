<?php 

namespace Morebec\YDB\Contract;

use Morebec\ValueObjects\ValueObjectInterface;

/**
 * TableSchemaInterface
 */
interface TableSchemaInterface extends ValueObjectInterface
{
    /**
     * Indicates if a column with a certain name exists
     * @param  string $columnName name of the column
     * @return bool
     */
    public function columnWithNameExists(string $columnName): bool;

    /**
     * @return string
     */
    public function getTableName(): string;

    /**
     * @return array
     */
    public function getColumns(): array;

    /**
     * Returns a column by its name or null if it was not found
     * @param  string $name name of the potential column
     * @return ColumnInterface|null
     */
    public function getColumnByName(string $name): ?ColumnInterface;
}