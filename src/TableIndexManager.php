<?php

namespace Morebec\YDB;

use Morebec\ValueObjects\File\Directory;
use Morebec\ValueObjects\File\File;
use Morebec\ValueObjects\File\Path;
use Morebec\YDB\Database\TableInterface;

/**
 * Class responsible for managing the indexing of table records
 */
class TableIndexManager
{
    /** @var TableInterface */
    private $table;

    function __construct(TableInterface $table)
    {
        $this->table = $table;
    }

    /**
     * Clears the indexes and rebuilds them
     */
    public function rebuildIndexes(): void
    {
        $this->clearIndexes();
        $this->updateIndexes();
    }

    /**
     * Updates the indexes of this table
     */
    public function updateIndexes()
    {
        $indexesDir = $this->getIndexesDirectory();
        
        foreach ($this->table->queryAll() as $record) {
            foreach ($this->table->getSchema()->getColumns() as $col) {
                if(!$col->isIndexed()) {
                    continue;
                }
                $fieldName = $col->getName();
                $fieldValue = $record->getFieldValue($fieldName);

                $index = $this->getIndexForColumnWithValue($col, $fieldValue);
                $index->indexRecord($record);
            }
        }
    }

    /**
     * Sorts the indexes
     */
    public function sortIndexes()
    {
        // Sort Indexes
        foreach ($this->getIndexes() as $index) {
            $index->sort();
        }
    }

    /**
     * Clears the index files
     */
    public function clearIndexes(): void
    {
        $indexesDir = $this->getIndexesDirectory();

        foreach ($this->getIndexes() as $index) {
            $index->clear();
        }
    }

    /**
     * Returns a list of all indexes
     * @return array
     */
    public function getIndexes(): array
    {
        $indexesDir = $this->getIndexesDirectory();

        $idxs = [];

        foreach ($indexesDir->getFiles() as $d) {
            foreach ($d->getFiles() as $f) {
                $fieldName = $d->getFilename();
                $col = $this->table->getColumnByName($fieldName);
                if(!$col) continue;

                $type = $col->getType(); 
                $idxs[] = new Index($fieldName, $type, $f);
            }
        }

        return $idxs;
    }

    /**
     * Returns the directory containing all the indexes of this manager's table
     * @return Directory
     */
    public function getIndexesDirectory(): Directory
    {
        $indexesDir = Directory::fromStringPath(
            $this->table->getDirectory()->getRealPath() . '/indexes' 
        );
        if(!$indexesDir->exists()) {
            mkdir($indexesDir->getRealPath());
        }

        return $indexesDir;
    }

    /**
     * Indicates if an index directory exists for a certain column with field value
     * @param  Column $column     column
     * @param  mixed  $fieldValue field value
     * @return bool             true if exists, otherwise false
     */
    public function indexForColumnWithValueExists(Column $column, $fieldValue): bool
    {
        $path = $this->getIndexPathForColumnWithValue($column, $fieldValue);
        $indexDir = new Directory($path);
        
        return $indexDir->exists();
    }

    /**
     * Returns the path to the index directory of a column field with a specific 
     * value
     * @param  Column $column     column
     * @param  mixed  $fieldValue value
     * @return Path
     */
    public function getIndexPathForColumnWithValue(Column $column, $fieldValue): Path
    {
        $indexesDir = $this->getIndexesDirectory();

        $fieldName = $column->getName();

        return new Path($indexesDir->getRealPath() . "/$fieldName");
    }


    /**
     * Returns the index files that matche a specific criterion
     * @param  Criterion $c criterion
     * @return array
     */
    public function getIndexesForCriterion(Criterion $c): array
    {
        // Filter indexes that match the criterion
        return array_filter($this->getIndexes(), static function ($i) use ($c) {
            $value = $i->getFieldValue();
            return $c->valueMatches($value);
        });
    }


    /**
     * Returns the index of a column
     * or null if there is no index on the column
     * @param  string $name name of the column
     * @return Index|null 
     */
    public function getIndexForColumnWithValue(Column $column, $fieldValue): Index
    {
        $path = $this->getIndexPathForColumnWithValue($column, $fieldValue);

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
}