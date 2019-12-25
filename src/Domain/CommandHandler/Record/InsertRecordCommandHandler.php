<?php

namespace Morebec\YDB\Domain\CommandHandler\Record;

use Assert\Assertion;
use Morebec\YDB\Command\Record\InsertRecordCommand;
use Morebec\YDB\Contract\Record;
use Morebec\YDB\Contract\TableSchemaInterface;
use Morebec\YDB\Enum\ColumnType;
use Morebec\YDB\Exception\TableNotFoundException;
use Morebec\YDB\Service\Database;
use Psr\Log\LogLevel;
use Symfony\Component\Yaml\Yaml;

/**
 * InsertRecordCommandHandler
 */
class InsertRecordCommandHandler
{
    /** @var Database */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function __invoke(InsertRecordCommand $command)
    {
        $tableName = $command->getTableName();

        // Make sure table exists
        if (!$this->database->tableExists($tableName)) {
            throw new TableNotFoundException($tableName);
        }

        $record = $command->getRecord();
        $recordId = $record->getId();

        $this->database->log(LogLevel::INFO, "Inserting record '$recordId' into table '$tableName' ...", [
            'record' => $record->toArray(),
            'record_id' => $recordId,
            'table_name' => $tableName
        ]);

        // Validate record
        $schema = $this->database->getTableSchema($tableName);
        $this->validateRecord($record, $schema);

        $id = $record->getId();


        $recordLocation = $this->database->getTableDirectory($tableName)->getRealPath() . "/$recordId.yaml";

        $filesystem = $this->database->getFilesystem();
        $yaml = Yaml::dump($record->toArray());

        $filesystem->dumpFile($recordLocation, $yaml);

        // TODO Dispatch Event RecordInsertedEvent

        $this->database->log(LogLevel::INFO, "Inserted record '$recordId' into table '$tableName'", [
            'record' => $record->toArray(),
            'record_id' => (string)$record->getId(),
            'record_location' => $recordLocation,
            'table_name' => $tableName
        ]);
    }

    /**
     * Validates a record with the database
     * @param  Record $record record
     */
    public function validateRecord(Record $record, TableSchemaInterface $schema): void
    {
        $tableName = $schema->getTableName();

        // Verify that every column is there
        foreach ($schema->getColumns() as $col) {
            $colName = $col->getName();

            // Make sure the field exist on the record
            // TODO: throw RecordMissingFieldException
            if (!$record->hasField($colName) && $colName !== 'id') {
                throw new \Exception(
                    sprintf("Invalid record, record '%s' does not have a field '%s'", $record->getId(), $colName)
                );
            }

            // Validate that the type is the right one
            $value = $record->getFieldValue($colName);

            $type = $col->getType();
            if ($type === ColumnType::STRING) {
                Assertion::string($value);
            } elseif ($type === ColumnType::BOOLEAN) {
                Assertion::boolean($value);
            } elseif ($type === ColumnType::INTEGER) {
                Assertion::integer($value);
            } elseif ($type === ColumnType::FLOAT) {
                Assertion::float($value);
            } elseif ($type === ColumnType::ARRAY) {
                Assertion::isArray($value);
            } else {
                // TODO
            }
        }

        // Verify that there is no extra fields
        $recordData = $record->toArray();
        foreach ($recordData as $key => $value) {
            Assertion::true(
                $schema->columnWithNameExists($key),
                "'$key' is not a column in table '$tableName'"
            );
        }
    }
}
