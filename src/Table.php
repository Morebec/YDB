<?php 

namespace Morebec\YDB;

use Assert\Assertion;
use Morebec\ValueObjects\File\Directory;
use Morebec\ValueObjects\File\File;
use Morebec\YDB\Database\ColumnInterface;
use Morebec\YDB\Database\QueryInterface;
use Morebec\YDB\Database\RecordIdInterface;
use Morebec\YDB\Database\RecordInterface;
use Morebec\YDB\Database\TableInterface;
use Morebec\YDB\Database\TableSchemaInterface;
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
    }

    /**
     * Takes a column and updates it to correspond to a new column
     * @param  ColumnInterface $column        base column
     * @param  ColumnInterface $updatedColumn updated column
     */
    public function updateColumn(ColumnInterface $column, ColumnInterface $updatedColumn)
    {
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
    }

    /**
     * Deletes a column from the table
     * @param  ColumnInterface $column column to delete
     */
    public function deleteColumn(ColumnInterface $column): void
    {
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
        
        // Update the records
        foreach ($records as $record) {
            $record->removeField($column->getName());
            $this->updateRecord($record);
        }
    }

    /**
     * Retrieves a column from the schema by its name
     * @param  string $name name of the column
     * @return ColumnInterface|null
     */
    public function getColumnByName(string $name): ?ColumnInterface
    {
        return $this->getSchema()->getColumnByName($name);
    }

    /**
     * Updates the schema on the file system
     * @param  TableSchemaInterface $schema new schema
     */
    public function updateSchema(TableSchemaInterface $schema): void
    {
        // Create schema
        $schemaYaml = Yaml::dump($schema->toArray());
        file_put_contents($this->getSchemaFile()->getRealPath(), $schemaYaml);   
    }

    /**
     * Adds a new record to the database
     * @param RecordInterface $record
     */
    public function addRecord(RecordInterface $record): void
    {
        $this->validateRecord($record);


        $id = $record->getId();
        $arr = $record->toArray();

        $filePath = $this->directory->getRealPath() . "/$id.yaml";

        $yaml = Yaml::dump($arr);

        file_put_contents($filePath, $yaml);

        $this->updateIndexes();
    }

    /**
     * Overwrites a record in the database by its id
     * @param  RecordIdInterface $id     id of the record to update
     * @param  RecordInterface   $record updated record
     */
    public function updateRecord(RecordInterface $record): void
    {
        $this->validateRecord($record);

        $id = $record->getId();
        $arr = $record->toArray();

        $filePath = $this->directory->getRealPath() . "/$id.yaml";

        $yaml = Yaml::dump($arr);
        file_put_contents($filePath, $yaml);

        $this->rebuildIndexes();
    }

    /**
     * Deletes a record by its id
     * @param  RecordIdInterface $id id of the record
     */
    public function deleteRecord(RecordIdInterface $id)
    {
        $filePath = $this->directory->getRealPath() . "/$id.yaml";
        unlink($filePath);

        $this->rebuildIndexes();
    }

    /**
     * Validates a record with the database
     * @param  RecordInterface $record record
     */
    public function validateRecord(RecordInterface $record): void
    {
        $arr = $record->toArray();

        // Verify that every column is there
        foreach ($this->schema->getColumns() as $col) {
            Assertion::keyExists($arr, $col->getName());

            $value = $arr[$col->getName()];
            if($col->getType() === ColumnType::STRING) {
                Assertion::string($value);

            } elseif ($col->getType() === ColumnType::BOOLEAN) {
                Assertion::boolean($value);

            } elseif ($col->getType() === ColumnType::FLOAT) {
                Assertion::float($value);

            } elseif ($col->getType() === ColumnType::ARRAY) {
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
    }

    /**
     * Tries to load a record from a file
     * @param  File   $file file to load
     * @return RecordInterface
     */
    private function loadRecordFromFile(File $file): RecordInterface
    {
        Assertion::true($file->exists(), 
            "Cannot load record, file '$file' does not exist."
        );

        $content = $file->getContent();
        $arr = Yaml::parse($content);

        Assertion::isArray($arr, "Malformed data file $file");

        Assertion::keyExists($arr, 'id');
        Assertion::notNull($arr['id']);

        return new Record(
            new RecordId($arr['id']),
            $arr
        );
    }

    /**
     * Returns all the records of the table in an array of Record objects
     * @return array
     */
    public function queryAll(): \Generator
    {
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
    }

    /**
     * Performs a query and returns all records that match.
     * @param  QueryInterface $query query
     * @return array
     */
    public function query(QueryInterface $query): array
    {
        // Use indexes
        $records = [];
        foreach ($query->getCriteria() as $c) {
            $fieldName = $c->getField();
            $fieldValue = $c->getValue();

            $column = $this->getColumnByName($fieldName);
            $index = $this->getIndexForColumnWithValue($column, $fieldValue);

            $ids = $index->getIds();

            foreach ($ids as $id) {
                $filePath = $this->getDirectory()->getRealPath() . "/$id.yaml";
                $file = File::fromStringPath($filePath);
                $records[] = $this->loadRecordFromFile(
                    $file
                );
            }
        }

        $records = array_filter($records, static function ($record) use ($query) {
            return $query->matches($record);
        });

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
        if (count($result) > 1) {
            throw new \Exception("The query $query returned more than one result");
        }

        if (empty($result)) {
            return null;
        }

        return $result->next();
    }

    /**
     * Clears the table from all its data
     */
    public function clear(): void
    {
        $fs = new Filesystem();
        foreach ($this->directory->getFiles() as $f) {
            if($f->getBasename() === TableSchema::SCHEMA_FILE_NAME){
                continue;
            }

            $fs->remove($f);
        }
    }

    /**
     * Returns the index of a column
     * or null if there is no index on the column
     * @param  string $name name of the column
     * @return Index|null 
     */
    public function getIndexForColumnWithValue(Column $column, $fieldValue): Index
    {
        $indexesDir = $this->getIndexesDirectory();

        $fieldName = $column->getName();

        // Create index directory if necessary
        $indexDir = Directory::fromStringPath(
            $indexesDir->getRealPath() . "/$fieldName"
        );
        if(!$indexDir->exists()) {
            mkdir($indexDir->getRealPath());
        }

        $filePath = $indexDir->getRealPath() . "/$fieldValue.idx";

        return new Index($fieldName, File::fromStringPath($filePath));
    }

    /**
     * Returns the directory containing all the indexes of this table
     * @return Directory
     */
    public function getIndexesDirectory(): Directory
    {
        $indexesDir = Directory::fromStringPath(
            $this->getDirectory()->getRealPath() . '/indexes' 
        );
        if(!$indexesDir->exists()) {
            mkdir($indexesDir->getRealPath());
        }

        return $indexesDir;
    }


    /**
     * Clears the indexes and rebuilds them
     */
    public function rebuildIndexes()
    {
        $indexesDir = $this->getIndexesDirectory();

        // Sort Indexes
        foreach ($indexesDir->getFiles() as $d) {
            foreach ($d->getFiles() as $f) {
                $index = new Index($f->getFilename(), $f);
                $index->clear();
            }
        }

        $this->updateIndexes();
    }

    /**
     * Updates the indexes of this table
     */
    public function updateIndexes()
    {
        $indexesDir = $this->getIndexesDirectory();
        
        foreach ($this->queryAll() as $record) {
            foreach ($this->schema->getColumns() as $col) {
                if(!$col->isIndexed()) {
                    continue;
                }
                $fieldName = $col->getName();
                $fieldValue = $record->getFieldValue($fieldName);

                $index = $this->getIndexForColumnWithValue($col, $fieldValue);
                $index->indexRecord($record);
            }
        }


        // Sort Indexes
        foreach ($indexesDir->getFiles() as $d) {
            foreach ($d->getFiles() as $f) {
                $index = new Index($f->getFilename(), $f);
                $index->sort();
            }
        }
    }
}