<?php 

namespace Morebec\YDB\Database;

/**
 * Interface for Database Tables
 */
interface TableInterface
{
    /**
     * Returns the name of the table
     * @return string
     */
    public function getName(): string;

    /**
     * Adds a new column to the table
     * @param ColumnInterface $column column
     * @param mixed          $defaultValue default value
     */
    public function addColumn(ColumnInterface $column, $defaultValue): void;

    /**
     * Takes a column and updates it to correspond to a new column
     * @param  ColumnInterface $column        base column
     * @param  ColumnInterface $updatedColumn updated column
     */
    public function updateColumn(ColumnInterface $column, ColumnInterface $updatedColumn);

    /**
     * Deletes a column from the table
     * @param  ColumnInterface $column column to delete
     */
    public function deleteColumn(ColumnInterface $column): void;

    /**
     * Retrieves a column from the schema by its name
     * @param  string $name name of the column
     * @return ColumnInterface|null
     */
    public function getColumnByName(string $name): ?ColumnInterface;

    /**
     * Adds a new record to the database
     * @param RecordInterface $record
     */
    public function addRecord(RecordInterface $record): void;

    /**
     * Overwrites a record in the database by its id
     * @param  RecordIdInterface $id     id of the record to update
     * @param  RecordInterface   $record updated record
     */
    public function updateRecord(RecordInterface $record): void;

    /**
     * Deletes a record by its id
     * @param  RecordIdInterface $id id of the record
     */
    public function deleteRecord(RecordIdInterface $id);

    /**
     * Returns all the records of the table in an array of Record objects
     * @return array
     */
    public function queryAll(): \Generator;

    /**
     * Performs a query and returns all records that match.
     * @param  QueryInterface $query query
     * @return array
     */
    public function query(QueryInterface $query): array;

    /**
     * Performs a query on the table and exepects that
     * the query returns only one result. If more than one
     * results are returned, throws an exception.
     * @param  QueryInterface $query query
     * @return Record|null      returns null if nothing found
     */
    public function queryOne(QueryInterface $query): ?RecordInterface;

    /**
     * Clears the table from all its data
     */
    public function clear(): void;

    /**
     * Returns the schema of the table
     * @return TableSchemaInterface
     */
    public function getSchema(): TableSchemaInterface;
}