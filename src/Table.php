<?php 

namespace Morebec\YDB;

use Assert\Assertion;
use Morebec\ValueObjects\File\Directory;
use Morebec\ValueObjects\File\File;
use Morebec\ValueObjects\File\Path;
use Morebec\YDB\Database\ColumnInterface;
use Morebec\YDB\Database\DatabaseInterface;
use Morebec\YDB\Database\QueryInterface;
use Morebec\YDB\Database\RecordIdInterface;
use Morebec\YDB\Database\RecordInterface;
use Morebec\YDB\Database\TableInterface;
use Morebec\YDB\Database\TableSchemaInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * Represents a table in a Yaml database
 */
class Table implements TableInterface
{
    /** @var TableSchemaInterface schema */
    private $schema;

    /** @var Directory directory where the table is located */
    private $directory;

    /** @var Filesystem */
    private $filesystem;

    /** @var TableIndexManager */
    private $indexManager;

    /** @var DatabaseInterface */
    private $database;

    /**
     * Constructs a new instance of a table object
     * @param string    $name      name of the table
     * @param Directory $directory directory where the table is
     * @param array     $columns   columns of the table
     */
    function __construct(TableSchemaInterface $schema, Directory $directory)
    {
        $this->schema = $schema;
        $this->directory = $directory;

        $this->filesystem = new Filesystem();

        $this->indexManager = new TableIndexManager($this);
    }

    /**
     * Returns the schema
     * @return TableSchemaInterface
     */
    public function getSchema(): TableSchemaInterface
    {
        return $this->schema;
    }

    /**
     * Returns the file where the schema is located
     * @return File
     */
    public function getSchemaFile(): File
    {
        $schemaFile = null;
        foreach($this->directory->getFiles() as $file) {
            if($file->getBasename() === TableSchema::SCHEMA_FILE_NAME) {
                return $file;
            }
        }

        $tableName = $this->getName();
        Assertion::notNull($schemaFile, "No schema was found for table $tableName");
    }

    /**
     * Returns the name of the table
     * @return string
     */
    public function getName(): string
    {
        return $this->schema->getTableName();
    }

    /**
     * Returns the directory of the table
     * @return Directory
     */
    public function getDirectory(): Directory
    {
        return $this->directory;
    }

    /**
     * Adds a new column to the table
     * @param ColumnInterface $column column
     * @param mixed           $defaultValue default value
     */
    public function addColumn(ColumnInterface $column, $defaultValue): void
    {
        $this->log(
            LogLevel::INFO, 
            sprintf("Adding column '%s' on table '%s' ... ", $column->getName(), $this->getName()),
            ['column' => $column->toArray()]
        );
        // Get records first, or else they wont pass validation
        $records = $this->queryAll();

        // Update the schema
        $schema = $this->getSchema();
        // Update the schema
        $columns = $schema->getColumns();
        $columns[] = $column;

        $newSchema = new TableSchema($this->getName(), $columns);
        $this->updateSchema($newSchema);
        
        // Update the records
        foreach ($records as $record) {
            $record->setFieldValue($column->getName(), $defaultValue);
            $this->updateRecord($record);
        }

        $this->log(
            LogLevel::INFO, 
            sprintf("Column '%s' added on table '%s'", $column->getName(), $this->getName()),
            ['column' => $column->toArray()]
        );
    }

    /**
     * Takes a column and updates it to correspond to a new column
     * @param  ColumnInterface $column        base column
     * @param  ColumnInterface $updatedColumn updated column
     */
    public function updateColumn(ColumnInterface $column, ColumnInterface $updatedColumn)
    {
        $this->log(
            LogLevel::INFO, 
            sprintf("Updating column '%s' on table '%s' ... ", 
                $column->getName(), 
                $this->getName()
            ),
            [
                'column' => $column->toArray(),
                'updated_column' => $updatedColumn->toArray()
            ]
        );
        // Get records first, or else they wont pass validation
        $records = $this->queryAll();

        // Update the schema
        $schema = $this->getSchema();
        $columns = $schema->getColumns();

        foreach ($columns as $key => $col) {
            if(!$column->isEqualTo($col)) {
                continue;
            }

            $columns[$key] = $updatedColumn;
        }

        $newSchema = new TableSchema($this->getName(), $columns);
        $this->updateSchema($newSchema);
        
        // Update the records
        foreach ($records as $record) {
            $v = $record->getFieldValue($column->getName());
            $record->setFieldValue($updatedColumn->getName(), $v);
            $record->removeField($column->getName());
            $this->updateRecord($record);
        }

        $this->log(
            LogLevel::INFO, 
            sprintf("Updated column '%s' on table '%s'.", 
                $column->getName(), 
                $this->getName()
            ),
            [
                'column' => $column->toArray(),
                'updated_column' => $updatedColumn->toArray()
            ]
        );
    }

    /**
     * Deletes a column from the table
     * @param  ColumnInterface $column column to delete
     */
    public function deleteColumn(ColumnInterface $column): void
    {
        $this->log(
            LogLevel::INFO, 
            sprintf("Deleting column '%s' on table '%s'...", 
                $column->getName(), 
                $this->getName()
            ),
            [
                'column' => $column->toArray(),
            ]
        );

        // Get records first, or else they wont pass validation
        $records = $this->queryAll();

        // Update the schema
        $schema = $this->getSchema();
        // Update the schema
        $columns = array_filter($schema->getColumns(), function($col) use ($column) {
            return !$col->isEqualTo($column);
        });

        $newSchema = new TableSchema($this->getName(), $columns);
        $this->updateSchema($newSchema);
        
        // Destroy index
        $this->indexManager->clearColumnIndexes($column);

        // Update the records
        foreach ($records as $record) {
            $record->removeField($column->getName());
            $this->updateRecord($record);
        }

        $this->log(
            LogLevel::INFO, 
            sprintf("Deleted column '%s' on table '%s'.", 
                $column->getName(), 
                $this->getName()
            ),
            [
                'column' => $column->toArray(),
            ]
        );
    }

    /**
     * Retrieves a column from the schema by its name
     * @param  string $name name of the column
     * @return ColumnInterface|null
     */
    public function getColumnByName(string $name): ?ColumnInterface
    {
        return $this->schema->getColumnByName($name);
    }

    /**
     * Returns all the columns of the table
     * @return array of Column objects
     */
    public function getColumns(): array
    {
        return $this->schema->getColumns();
    }

    /**
     * Updates the schema on the file system
     * @param  TableSchemaInterface $schema new schema
     */
    public function updateSchema(TableSchemaInterface $schema): void
    {
        $schemaPath = $this->getSchemaFile()->getRealPath();
        $schemaFile = File::fromStringPath($schemaPath);

        $this->log(
            LogLevel::INFO, 
            sprintf("Updating schema on table '%s'...", 
                $this->getName()
            ),
            [
                'schema' => $schema->toArray(),
                'schema_path' => $schemaPath
            ]
        );
        // Create schema
        $lock = $this->database->waitUntilFileUnlocked($schemaFile);
        $schemaYaml = Yaml::dump($schema->toArray());
        $this->filesystem->dumpFile($schemaFile, $schemaYaml);
        $lock->release();

        $this->schema = $schema;

        // Rebuild indexes
        foreach ($this->queryAll() as $record) {
            $this->indexManager->indexRecord($record);
        }

        $this->log(
            LogLevel::INFO, 
            sprintf("Updated schema on table '%s'.", 
                $this->getName()
            ),
            [
                'schema' => $schema->toArray(),
            ]
        );   
    }

    /**
     * Adds a new record to the database
     * @param RecordInterface $record
     */
    public function addRecord(RecordInterface $record): void
    {
        $this->log(
            LogLevel::INFO, 
            sprintf("Adding record '%s' in table '%s'.", 
                $record->getId(), 
                $this->getName()
            ),
            [
                'record_id' => $record->getId(),
                'record' => $record->toArray()
            ]
        );

        $this->validateRecord($record);


        $id = $record->getId();
        $arr = $record->toArray();

        $filePath = $this->directory->getRealPath() . "/$id.yaml";

        $yaml = Yaml::dump($arr);

        $lock = $this->database->waitUntilFileUnlocked(File::fromStringPath($filePath));
        $this->filesystem->dumpFile($filePath, $yaml);
        $lock->release();

        $this->indexManager->indexRecord($record);

        $this->log(
            LogLevel::INFO, 
            sprintf("Added record '%s' in table '%s'.", 
                $record->getId(), 
                $this->getName()
            ),
            [
                'record_id' => $record->getId(),
                'record' => $record->toArray()
            ]
        );
    }

    /**
     * Overwrites a record in the database by its id
     * @param  RecordIdInterface $id     id of the record to update
     * @param  RecordInterface   $record updated record
     */
    public function updateRecord(RecordInterface $record): void
    {
        $this->log(
            LogLevel::INFO, 
            sprintf("Updating record '%s' in table '%s'...", 
                $record->getId(), 
                $this->getName()
            ),
            [
                'record' => $record->toArray()
            ]
        );

        $id = $record->getId();
        $arr = $record->toArray();
        $filePath = $this->directory->getRealPath() . "/$id.yaml";

        $lock = $this->database->waitUntilFileUnlocked(File::fromStringPath($filePath));
        $yaml = Yaml::dump($arr);
        $this->filesystem->dumpFile($filePath, $yaml);
        $lock->release();

        $this->indexManager->updateRecordIndexes($record);

        $this->log(
            LogLevel::INFO, 
            sprintf("Updated record '%s' in table '%s'.", 
                $record->getId(), 
                $this->getName()
            ),
            [
                'record' => $record->toArray()
            ]
        );
    }

    /**
     * Deletes a record
     * @param  RecordIdInterface $record record
     */
    public function deleteRecord(RecordInterface $record)
    {
        $id = $record->getId();
        $this->log(
            LogLevel::INFO, 
            sprintf("Deleting record '%s' on table '%s'...", $id, $this->getName()),
            [
                'record' => $record->toArray()
            ]
        );

        $filePath = $this->directory->getRealPath() . "/$id.yaml";
        $lock = $this->database->waitUntilFileUnlocked(File::fromStringPath($filePath));
        unlink($filePath);
        $lock->release();

        $this->indexManager->clearRecordIndexes($record);

        $this->log(
            LogLevel::INFO, 
            sprintf("Deleted record '%s' on table '%s'.", $id, $this->getName()),
            [
                'record_id' => $id
            ]
        );
    }

    /**
     * Validates a record with the database
     * @param  RecordInterface $record record
     */
    public function validateRecord(RecordInterface $record): void
    {
        $this->log(
            LogLevel::INFO, 
            sprintf("Validating record '%s' in table '%s'...", 
                $record->getId(), 
                $this->getName()
            ),
            [
                'record' => $record->toArray()
            ]
        );

        $arr = $record->toArray();

        // Verify that every column is there
        foreach ($this->schema->getColumns() as $col) {
            $colName = $col->getName();
            Assertion::keyExists($arr, $colName, 
                sprintf("Invalid record, record '%s' does not have a field '%s'", $record->getId(), $colName)
            );

            $value = $arr[$colName];
            $type = $col->getType();
            if($type === ColumnType::STRING) {
                Assertion::string($value);

            } elseif ($type === ColumnType::BOOLEAN) {
                Assertion::boolean($value);

            } elseif ($type === ColumnType::FLOAT) {
                Assertion::float($value);

            } elseif ($type === ColumnType::ARRAY) {
                Assertion::isArray($value);
            }
        }

        // Verify that there is not more data in the record than the table supports
        $tableName = $this->getName();
        foreach ($arr as $key => $value) {
            Assertion::true($this->schema->columnWithNameExists($key), 
                "'$key' is not a column in table '$tableName'"
            );
        }

        $this->log(
            LogLevel::INFO, 
            sprintf("Validated record '%s' in table '%s'...", 
                $record->getId(), 
                $this->getName()
            ),
            [
                'record' => $record->toArray()
            ]
        );
    }

    /**
     * Tries to load a record from a file
     * @param  File   $file file to load
     * @return RecordInterface
     */
    private function loadRecordFromFile(File $file): RecordInterface
    {
        $this->log(
            LogLevel::INFO, 
            sprintf("Loading record in table '%s' from file ...", 
                $this->getName(),
                $file
            ),
            [
                'record_id' => $file->getFilename(),
                'file' => $file->getRealPath(),
                'table' => $this->getName()
            ]
        );
        Assertion::true($file->exists(), 
            "Cannot load record, file '$file' does not exist."
        );

        $lock = $this->database->waitUntilFileUnlocked(File::fromStringPath($file));
        $content = $file->getContent();
        $lock->release();
        $arr = Yaml::parse($content);

        Assertion::isArray($arr, "Malformed data file $file");

        Assertion::keyExists($arr, 'id');
        Assertion::notNull($arr['id']);

        $r = new Record(
            new RecordId($arr['id']),
            $arr
        );

        $this->log(
            LogLevel::INFO, 
            sprintf("Loaded record '%s' in table '%s' from file.", 
                $r->getId(),
                $this->getName()
            ),
            [
                'record_id' => $r->getId(),
                'file' => $file->getRealPath(),
                'table' => $this->getName()
            ]
        );

        return $r;
    }

    /**
     * Returns all the records of the table in an array of Record objects
     * @return array
     */
    public function queryAll(): \Generator
    {
        $this->log(
            LogLevel::INFO, 
            sprintf("Querying all records from table '%s' ...", 
                $this->getName()
            ),
            [
                'table' => $this->getName()
            ]
        );

        $files = $this->directory->getFiles();
        $schemaFile = $this->getSchemaFile();
        $files = array_filter(iterator_to_array($files), static function ($f) use ($schemaFile) {
            $isSchema = $f->isEqualTo($schemaFile);
            $isDataFile = $f->getExtension() == 'yaml';
            return  !$isSchema && $isDataFile;
        });

        foreach ($files as $file) {
            yield $this->loadRecordFromFile($file);
        }

        $this->log(
            LogLevel::INFO, 
            sprintf("Queried all records from table '%s' ...", 
                $this->getName()
            ),
            [
                'table' => $this->getName()
            ]
        );
    }



    /**
     * Performs a query and returns all records that match.
     * @param  QueryInterface $query query
     * @return array
     */
    public function query(QueryInterface $query): array
    {
        $queryTime = time();
        $this->log(
            LogLevel::INFO, 
            sprintf("Querying records from table '%s' ...", 
                $this->getName()
            ),
            [
                'table' => $this->getName(),
                'query' => (string)$query
            ]
        );

        $records = [];

        // We need to determine the records that need to be loaded
        // for the query evaluation, that is, if we ever need to load
        // all records to make the checks. This result is called the source.
        // It can therefore either be [all|index]
        // The rules are
        // Both "ands" and "ors" groups of criteria must be checked.
        // 
        // If at least one criterion of the "and" group can work with
        // indexes, the source of the "and" group shall be index
        // Inversely, if at least one "or" criterion requires all records,
        // the source of the "or" group shall be all
        // 
        // Finally if both groups can rely on indexes only we will load form the indexes
        
        $andCriterias = $query->getAndCriteria();
        $andCanOnlyRelyOnIndexes = empty($andCriterias) ? true : false;
        $andIds = [];
        foreach ($andCriterias as $c) {
            $col = $this->getColumnByName($c->getField());
            if(!$col) continue;

            if (!$col->isIndexed()) {
                continue;
            }
            
            $andCanOnlyRelyOnIndexes = true;
            $ids = [];
            $index = $this->indexManager->getIndexForCriterion($c);
            $ids = array_merge($ids, $index->getIds());

            // We'll want to keep the smallest subset of ids
            if (count($ids) < count($andIds) || empty($andIds)) {
                $andIds = $ids;
            }
        }

        $orCanOnlyRelyOnIndexes = true;
        $orIds = [];
        foreach ($query->getOrCriteria() as $c) {
            $col = $this->getColumnByName($c->getField());
            if(!$col) continue;

            if (!$col->isIndexed()) {
                $orCanOnlyRelyOnIndexes = false;
                continue;
            }
            
            $ids = [];
            $indexes = $this->indexManager->getIndexesForCriterion($c);
            foreach ($indexes as $index) {
                $ids = array_merge($ids, $index->getIds());
            }

            // We'll want to keep the smallest subset of ids
            if (count($ids) < count($andIds) || empty($andIds)) {
                $andIds = $ids;
            }
        }

        $records = [];
        if ($andCanOnlyRelyOnIndexes && $orCanOnlyRelyOnIndexes) {
            foreach ($ids as $id) {
                $this->log(
                    LogLevel::INFO, 
                    sprintf("Criterion fetched indexes", 
                        $this->getName()
                    ),
                    [
                        'criterion' => (string)$c,
                        '$ids' => $ids
                    ]
                );
                $filePath = $this->getDirectory()->getRealPath() . "/$id.yaml";
                $file = File::fromStringPath($filePath);
                $records[] = $this->loadRecordFromFile(
                    $file
                );
            }
        } else {
            $records = iterator_to_array($this->queryAll());
        }

        // Filter records
        $records = array_filter($records, static function($rec) use ($query) {
            return $query->matches($rec);
        });


        // Use array values to make sure keys are sequential
        $records = array_values(array_unique($records));

        $this->log(
            LogLevel::INFO, 
            sprintf("Queried records from table '%s' ...", 
                $this->getName()
            ),
            [
                'table' => $this->getName(),
                'query' => (string)$query,
                'count' => count($records),
                'strategy' => [
                    'use_index_for_inclusive' => $andCanOnlyRelyOnIndexes,
                    'use_index_for_exclusive'=> $orCanOnlyRelyOnIndexes,
                ],
                'time' => time() - $queryTime
            ]
        );

        return $records;
    }

    /**
     * Performs a query on the table and exepects that
     * the query returns only one result. If more than one
     * results are returned, throws an exception.
     * @param  QueryInterface $query query
     * @return Record|null      returns null if nothing found
     */
    public function queryOne(QueryInterface $query): ?RecordInterface
    {
        $result = $this->query($query);
        $nbResults = count($result);
        if ($nbResults > 1) {
            throw new \Exception("The query $query returned more than one result");
        }

        if ($nbResults === 0) {
            return null;
        }

        return $result[0];
    }

    /**
     * Clears the table from all its data
     */
    public function clear(): void
    {
        $this->log(
            LogLevel::INFO, 
            sprintf("Clearing records from table '%s' ...", 
                $this->getName()
            ),
            [
                'table' => $this->getName(),
            ]
        );
           
        foreach ($this->directory->getFiles() as $f) {
            if($f->getBasename() === TableSchema::SCHEMA_FILE_NAME){
                continue;
            }

            $lock = $this->database->waitUntilFileUnlocked(File::fromStringPath($f));
            $this->filesystem->remove($f);
            $lock->release();
        }

        $this->log(
            LogLevel::INFO, 
            sprintf("Cleared records from table '%s' ...", 
                $this->getName()
            ),
            [
                'table' => $this->getName(),
            ]
        );
    }

    /**
     * Logs a message
     * @param  string $level   level
     * @param  string $message message
     * @param  array  $context context
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $this->database->log($level, $message, $context);
    }

    /**
     * @param mixed $database
     *
     * @return self
     */
    public function setDatabase(DatabaseInterface $database)
    {
        $this->database = $database;

        return $this;
    }
}