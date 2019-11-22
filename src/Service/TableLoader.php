<?php

namespace Morebec\YDB\Service;

use Assert\Assertion;
use Morebec\ValueObjects\File\Directory;
use Morebec\ValueObjects\File\File;
use Morebec\YDB\Contract\TableInterface;
use Morebec\YDB\Contract\TableSchemaInterface;
use Morebec\YDB\Entity\Column;
use Morebec\YDB\Entity\Table;
use Morebec\YDB\Entity\TableSchema;
use Morebec\YDB\Exception\TableNotFoundException;
use Morebec\YDB\Exception\TableSchemaNotFoundException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class responsible for loading tables
 */
class TableLoader
{
    /** @var TableManager */
    private $tableManager;

    /**
     * Constructs an instance of this loader
     * @param Directory $tableManager directory containing all tables
     */
    public function __construct(TableManager $tableManager)
    {
        $this->tableManager = $tableManager;
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

        if (!$directory->exists()) {
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
        $files = $this->tableManager->getTablesDirectory();
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
    public function loadTableSchemaByName(string $tableName): TableSchemaInterface
    {
        $directory = $this->getTableDirectory($tableName);

        // Load schema
        $schemaFile = File::fromStringPath($directory . "/" . TableSchema::SCHEMA_FILE_NAME);
        
        if (!$schemaFile->exists()) {
            throw new TableSchemaNotFoundException($tableName);
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
        return Directory::fromStringPath(
            $this->tableManager->getTablesDirectory() . "/$tableName"
        );
    }
}
