<?php 

namespace Morebec\YDB\Service;

use Morebec\ValueObjects\File\Directory;

/**
 * Class responsible for loading tables
 */
class TableLoader
{
    /** @var Directory directory containing all tables */
    private $tablesDir;

    /**
     * Constructs an instance of this loader
     * @param Directory $tablesDir directory containing all tables
     */
    function __construct(Directory $tablesDir)
    {
        $this->tablesDir = $tablesDir;
    }

    /**
     * Tries to load a table by its name if it does not exist
     * throws a TableNotFoundException
     * @param  string $tableName name of the table
     * @return TableInterface
     */
    public function loadTableByName(string $tableName): TableInterface
    {
        $directory = $this->getTableDirectory($tableName);

        if(!$directory->exists()) {
            throw new TableNotFoundException($tableName);
        }

        $schema = $this->loadTableSchemaByName($tableName);
        $table = new Table($schema, $directory);
        return $table;
    }

    /**
     * Loads all the tables and returns them
     * @return array array of table objects
     */
    public function loadTables(): array
    {
        $files = $this->tablesDir;
        $tables = [];
        foreach ($files as $file) {
            if (!$file instanceof Directory) {
                continue;
            }
            $tables[] = $this->loadTableByName($file);
        }
        return $tables;
    }

    /**
     * Indicates if a table exists or not
     * @param  string $tableName name of the table
     * @return bool            true if table exists, otherwise false
     */
    public function tableExists(string $tableName): bool
    {
        return $this->getTableDirectory($tableName)->exists();
    }

    /**
     * Tries to load a table schema from a table directory
     * @param  Directory $directory directory
     * @return TableSchemaInterface
     */
    private function loadTableSchemaByName(string $tableName): TableSchemaInterface
    {
        $directory = $this->getTableDirectory($tableName);

        // Load schema
        $schemaFile = File::fromStringPath($directory . "/" . TableSchema::SCHEMA_FILE_NAME);
        
        if(!$schemaFile->exists()) {
            throw new TableSchemaNotFoundException($tableName, $TableSchema);
        }

        $schema = Yaml::parse($schemaFile->getContent());
        $columns = [];

        // Validate content
        Assertion::keyExists($schema, 'columns');
        foreach ($schema['columns'] as $col) {
            $columns[] = Column::fromArray($col);
        }

        Assertion::keyExists($schema, 'table_name');
        return new TableSchema($schema['table_name'], $columns);
    }

    /**
     * Returns the directory of table by its name
     * @param  string $tableName name of the table
     * @return Directory
     */
    private function getTableDirectory(string $tableName): Directory
    {
        return Directory::fromStringPath($this->tablesDir . "/$tableName");
    }
}