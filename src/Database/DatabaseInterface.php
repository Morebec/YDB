<?php 

namespace Morebec\YDB\Database;

/**
 * DatabaseInterface
 */
interface DatabaseInterface
{
    /**
     * Clears the database
     */
    public function clear(): void;

    /**
     * Creates the database
     * @return
     */
    public function create(): void;

    /**
     * Creates a table from a schema
     * @param  TableInterface  $schema schema
     */
    public function createTable(TableSchemaInterface $schema): TableInterface;

    /**
     * Updates the schema of a table and returns an updated
     * instance of the table
     * @param  Table  $table Table to update
     * @param  Table  $schema new schema
     * @return TableInterface  updated instance of table
     */
    public function updateTable(TableInterface $table, TableSchemaInterface $schema): TableInterface;

    /**
     * Deletes a table
     * @param  Table  $table table
     */
    public function deleteTable(TableInterface $table);

    /**
     * Indicates if a table exists or not
     * @param  TableInterface $table table
     * @return bool                true if table exists otherwise false
     */
    public function tableExists(TableInterface $table): bool;

    /**
     * Returns a list of all the tables
     * @return array
     */
    public function getTables(): array;
}