<?php

namespace Morebec\YDB\Entity;

use Morebec\ValueObjects\File\File;
use Morebec\YDB\Contract\TableInterface;

/**
 * Table
 */
class Table implements TableInterface
{
    /** @var TableSchemaInterface schema */
    private $schema;
    
    /** @var Directory directory where the table is located */
    private $directory;

    /**
     * Creates a Table instance from a schema and a directory
     * @param TableSchemaInterface $schema    schema
     * @param Directory            $directory table directory
     */
    public function __construct(TableSchemaInterface $schema, Directory $directory)
    {
        $this->schema = $schema;
        $this->directory = $directory;
    }

    /**
     * Returns the file containing the schema
     * @return File
     */
    public function getSchemaFile(): File
    {
        $file = File::fromStringPath($this->directory . '/' . TableSchema::SCHEMA_FILE_PATH);
        return $file;
    }

    /**
     * @return TableSchemaInterface
     */
    public function getSchema(): TableSchemaInterface
    {
        return $this->schema;
    }

    /**
     * @return Directory
     */
    public function getDirectory(): Directory
    {
        return $this->directory;
    }
}
