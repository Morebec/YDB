<?php

namespace Morebec\YDB;

use Morebec\ValueObjects\File\Directory;
use Morebec\ValueObjects\File\File;
use Morebec\ValueObjects\File\Path;
use Morebec\YDB\Database\ColumnInterface;
use Morebec\YDB\Database\RecordInterface;
use Morebec\YDB\Database\TableInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class responsible for managing the indexing of table records
 */
class TableIndexManager
{
    /** Name of the directory where all indexes are sotred */
    const INDEXES_DIRECTORY_NAME = 'indexes';

    /** @var TableInterface */
    private $table;

    /** @var Directory directory where all indexes are stored */
    private $indexesDirectory;

    /** @var Filesystem */
    private $filesystem;

    function __construct(TableInterface $table)
    {
        $this->table = $table;

        $this->filesystem = new Filesystem();

        // Indexes dir
        $indexesDir = Directory::fromStringPath(
            sprintf(
                "%s/%s", 
                $this->table->getDirectory()->getRealPath(),
                self::INDEXES_DIRECTORY_NAME
            )
        );

        if(!$indexesDir->exists()) {
            mkdir($indexesDir->getRealPath());
        }

        $this->indexesDirectory = $indexesDir;
    }

    /**
     * Indexes a record
     * @param  Record $record record
     */
    public function indexRecord(RecordInterface $record): void
    {   
        $columns = $this->table->getColumns();

        foreach ($columns as $col) {
            $value = $record->getFieldValue($col->getName());
            $index = $this->getIndexForColumnWithValue($col, $value);
            $index->indexRecord($record);
        }
    }


    /**
     * Removes the record from all indexes
     * @param  Record $record record
     */
    public function clearRecordIndexes(RecordInterface $record): void
    {
        $columns = $this->table->getColumns();

        foreach ($columns as $col) {
            $value = $record->getFieldValue($col->getName());
            $index = $this->getIndexForColumnWithValue($col, $value);
            $index->removeRecord($record);
        }
    }

    /**
     * Clears a column's indexes
     */
    public function clearColumnIndexes(ColumnInterface $column): void
    {
        $this->filesystem->remove(
            $this->indexesDirectory->getRealPath() . '/' . $column->getName()
        );
    }

    /**
     * Updates the indexes of a record
     * @param  Record $record record
     */
    public function updateRecordIndexes(RecordInterface $record): void
    {
        $this->clearRecordIndexes($record);
        $this->indexRecord($record);
    }


    /**
     * Returns the index of a column
     * or null if there is no index on the column
     * @param  string $name name of the column
     * @return Index|null 
     */
    public function getIndexForColumnWithValue(ColumnInterface $column, $fieldValue): Index
    {
        $path = $this->buildIndexPathForColumnWithValue($column, $fieldValue);

        $indexDir = new Directory($path);
        if(!$indexDir->exists()) {
            mkdir($indexDir->getRealPath());
        }

        $filePath = $indexDir->getRealPath() . "/$fieldValue" . Index::FILE_EXTENSION;
        $fieldName = $column->getName();

        return new Index(
            $fieldName, 
            $column->getType(), 
            File::fromStringPath($filePath)
        );
    }

    /**
     * Returns the path to the index directory of a column field with a specific 
     * value
     * @param  Column $column     column
     * @param  mixed  $fieldValue value
     * @return Path
     */
    public function buildIndexPathForColumnWithValue(ColumnInterface $column, $fieldValue): Path
    {
        $indexesDir = $this->indexesDirectory;

        $fieldName = $column->getName();

        return new Path($indexesDir->getRealPath() . "/$fieldName");
    }

    /**
     * Returns the index files that matche a specific criterion
     * @param  Criterion $c criterion
     * @return array
     */
    public function getIndexForCriterion(Criterion $c): Index
    {
        $column = $this->table->getColumnByName($c->getField());
        $value = $c->getValue();
        $index = $this->getIndexForColumnWithValue($column, $value);

        return $index;
    }
}