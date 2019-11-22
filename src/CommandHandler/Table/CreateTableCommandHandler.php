<?php

namespace Morebec\YDB\CommandHandler\Table;

use Morebec\YDB\Command\Table\CreateTableCommand;
use Morebec\YDB\Contract\TableSchemaInterface;
use Morebec\YDB\Entity\Column;
use Morebec\YDB\Entity\TableSchema;
use Morebec\YDB\Enum\ColumnType;
use Morebec\YDB\Event\Table\TableCreatedEvent;
use Morebec\YDB\Exception\TableAlreadyExistsException;
use Morebec\YDB\Service\Database;
use Psr\Log\LogLevel;
use Symfony\Component\Yaml\Yaml;

/**
 * CreateTableCommandHandler
 */
class CreateTableCommandHandler
{
    /** @var Database */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function __invoke(CreateTableCommand $command)
    {
        $schema = $command->getTableSchema();
        $tableName = $schema->getTableName();

        $this->database->log(
            LogLevel::INFO,
            "Creating table '$tableName' ...",
            ['table_name' => $tableName]
        );

        // Check if table already exists, if so throw exception
        if ($this->database->tableExists($tableName)) {
            throw new TableAlreadyExistsException($tableName);
        }

        // Create table directory
        $path = $this->database->getPath() . '/' . Database::TABLES_DIR_NAME . "/$tableName";
        $this->createTableDirectory($path);

        // Create schema file
        $schema = $command->getTableSchema();

        // Make sure there is an id field
        if (!$schema->columnWithNameExists('id')) {
            $schemaColumns = $schema->getColumns();
            $schemaColumns[] = new Column('id', ColumnType::STRING(), true /* indexed */);

            $schema = new TableSchema($schema->getTableName(), $schemaColumns);
        }

        $this->createTableSchema($path, $schema);

        $this->database->log(
            LogLevel::INFO,
            "Table '$tableName' created.",
            ['table_name' => $tableName]
        );

        $this->database->dispatchEvent(TableCreatedEvent::NAME, new TableCreatedEvent($tableName));
    }

    /**
     * Creates the directory for the table
     * @param  string $tablePath path to the table directory
     */
    private function createTableDirectory(string $tablePath): void
    {
        $filesystem = $this->database->getFilesystem();
        $filesystem->mkdir($tablePath);
    }

    /**
     * Creates the schema for the table
     * @param  string $tablePath path to the table directory
     */
    private function createTableSchema(string $tablePath, TableSchemaInterface $schema): void
    {
        $schemaPath = $tablePath . '/' . TableSchema::SCHEMA_FILE_NAME;
        $schemaYaml = Yaml::dump($schema->toArray());
        file_put_contents($schemaPath, $schemaYaml);
    }
}
