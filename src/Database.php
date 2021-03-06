<?php

namespace Morebec\YDB;

use Assert\Assertion;
use Morebec\ValueObjects\File\Directory;
use Morebec\ValueObjects\File\File;
use Morebec\YDB\Column;
use Morebec\YDB\Database\DatabaseInterface;
use Morebec\YDB\Database\TableInterface;
use Morebec\YDB\Database\TableSchemaInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Yaml\Yaml;


/**
 * Database
 */
class Database implements DatabaseInterface
{
    /** @var Directory root */
    private $root;

    /** @var Filesystem */
    private $fileSystem;

    /** @var LoggerInterfae */
    private $logger;

    /** @var Directory Tables directory */
    private $tablesDir;

    /** @var DatabaseConfig */
    private $config;

    /**
     * Constructs a database instance
     * @param Path $root root directory where the database is located
     */
    function __construct(DatabaseConfig $config)
    {
        $this->config = $config;

        $this->root = $config->getDatabasePath();

        $this->fileSystem = new Filesystem();

        $this->fileLocker = new FileLocker();

        // Create the root directory if it does not already exists
        if(!$this->root->exists()) {
            $this->create();
        }

        // Tables Directory
        $this->tablesDir = Directory::fromStringPath($this->root->getRealPath() . '/tables');
        if(!$this->tablesDir->exists()) {
            mkdir($this->tablesDir);
        }  

        // Prepare logger
        if($this->config->isLoggingEnabled()) {
            $this->logger = new \Katzgrau\KLogger\Logger($this->root->getRealPath() . '/logs');
        }
    }

    /**
     * Returns the root
     * @return Root
     */
    public function getRoot(): Directory
    {
        return $this->root;
    }

    /**
     * Returns the directory where tables are stored
     * @return Directory
     */
    public function getTablesDir(): Directory
    {
        return $this->tablesDir;
    }

    /**
     * Creates the database
     * @return
     */
    public function create(): void
    {
        mkdir($this->root);
    }

    /**
     * Clears the database
     */
    public function clear(): void
    {
        $tables = $this->getTables();
        foreach ($tables as $table) {
            $table->clear();
        }
    }

    /**
     * Deletes the database from the file system
     */
    public function delete(): void
    {
        $this->fileSystem->remove($this->root);
    }

    /**
     * Creates a table from a schema
     * @param  TableInterface  $schema schema
     */
    public function createTable(TableSchemaInterface $schema): TableInterface
    {
        $tableName = $schema->getTableName();

        // Make sure the table does not already exists
        Assertion::null($this->getTableByName($tableName), 
            "Cannot create table '$tableName' as it already exists."
        );

        // Create directory
        $path = $this->tablesDir . "/$tableName";
        mkdir($path);

        // Create schema
        $schemaPath = $path . '/' . TableSchema::SCHEMA_FILE_NAME;
        $schemaYaml = Yaml::dump($schema->toArray());
        file_put_contents($schemaPath, $schemaYaml);

        $table = new Table($schema, Directory::fromStringPath($path));
        $table->setDatabase($this);

        return $table;
    }

    /**
     * Updates the schema of a table
     * @param  Table  $table Table to update
     * @param  Table  $schema new schema
     */
    public function updateTable(
        TableInterface $table, 
        TableSchemaInterface $schema
    ): TableInterface
    {
        $directory = $table->getDirectory();

        // Check if we need to rename the table
        if($table->getName() !== $schema->getTableName()) {
            $directory = Directory::fromStringPath(
                $this->tablesDir->getRealPath() . '/' . $schema->getTableName()
            );
            rename(
                $table->getDirectory()->getRealPath(), 
                $directory->getRealPath()
            );
            $table = $this->loadTable($directory);
        }


        // Dump schema
        $schemaYaml = Yaml::dump($schema->toArray());
        file_put_contents($table->getSchemaFile(), $schemaYaml);

        return $this->loadTable($directory);
    }

    /**
     * Drops a table
     * @param  Table  $table table
     */
    public function deleteTable(TableInterface $table)
    {
        $this->fileSystem->remove($table->getDirectory());
    }

    /**
     * Indicates if a table exists or not
     * @param  TableInterface $table table
     * @return bool                true if table exists otherwise false
     */
    public function tableExists(TableInterface $table): bool
    {
        return $table->getDirectory()->exists();
    }

    /**
     * Returns a list of all the tables
     * @return array
     */
    public function getTables(): array
    {
        $files = $this->tablesDir->getFiles();
        $tables = [];
        foreach ($files as $file) {
            if (!$file instanceof Directory) {
                continue;
            }

            $tables[] = $this->loadTable($file);
        }

        return $tables;
    }

    /**
     * Returns a table by its name or null if none found
     * @param  string $tableName name of the table
     * @return TableInterface|null
     */
    public function getTableByName(string $tableName): ?TableInterface
    {   
        $tables = $this->getTables();
        foreach ($tables as $table) {
            if($table->getName() === $tableName) {
                return $table;
            }
        }

        return null;
    }

    /**
     * Tries to load a table from a directory
     * @param  Directory $directory directory
     * @return TableInterface
     */
    private function loadTable(Directory $directory): TableInterface
    {
        Assertion::true($directory->exists(), 
            "Could not load table from directory '$directory', as it does not exist"
        );
        $schema = $this->loadTableSchema($directory);

        $table = new Table($schema, $directory);

        $table->setDatabase($this);
        return $table;
    }

    /**
     * Tries to load a table schema from a table directory
     * @param  Directory $directory directory
     * @return TableSchemaInterface
     */
    private function loadTableSchema(Directory $directory): TableSchemaInterface
    {
        $tableName = $directory->getBasename();

        // Load schema
        $schemaFile = null;
        foreach($directory->getFiles() as $file) {
            if($file->getBasename() === TableSchema::SCHEMA_FILE_NAME) {
                $schemaFile = $file;
            }
        }
        Assertion::notNull($schemaFile, "No schema was found for table $tableName");

        $schema = Yaml::parse($file->getContent());

        $columns = [];

        Assertion::keyExists($schema, 'columns');
        foreach ($schema['columns'] as $col) {
            $columns[] = Column::fromArray($col);
        }

        Assertion::keyExists($schema, 'table_name');
        return new TableSchema($schema['table_name'], $columns);
    }

    /**
     * Waits until a file is unlocked, and when it is returns a
     * lock to be released 
     * @param  File   $file file to lock
     * @return LockInterface
     */
    public function waitUntilFileUnlocked(File $file): LockInterface
    {
        $lock = $this->fileLocker->createFileLock($file);
        $lock->acquire(true);
        return $lock;
    }

    /**
     * Logs a message
     * @param  string $level   level
     * @param  string $message message
     * @param  array  $context context
     */
    public function log(string $level, string $message, array $context = []): void
    {
        if(!$this->logger){
            return;
        }

        $this->logger->log($level, $message, $context);
    }

    /**
     * @return DatabaseConfig
     */
    public function getConfig(): DatabaseConfig
    {
        return $this->config;
    }
}