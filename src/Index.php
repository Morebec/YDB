<?php 

namespace Morebec\YDB;

use Assert\Assertion;
use Morebec\ValueObjects\File\File;
use Morebec\YDB\ColumnType;
use Morebec\YDB\Database\RecordInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Represents a database index
 */
class Index
{
    /** @var string extension of index files */
    const FILE_EXTENSION = '.idx';

    /** @var string name of the field that is indexed */
    private $fieldName;

    /** @var ColumnType type of the field */
    private $fieldType;

    /** @var File */
    private $file;

    function __construct(string $fieldName, ColumnType $fieldType, File $file)
    {
        Assertion::notBlank($fieldName);
        Assertion::false($fieldType->isEqualTo(ColumnType::ARRAY()), 
            "Cannot index field '$fieldName', because it is an array."
        );

        $this->fieldName = $fieldName;
        $this->fieldType = $fieldType;
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * Sorts the index file
     */
    public function sort(): void
    {
        $filePath = $this->file->getRealPath();
        $data = file($filePath);
        natsort($data);
        file_put_contents($filePath, $data);
    }

    /**
     * Returns the value of the index
     * @return mixed
     */
    public function getFieldValue() {
        $value = $this->file->getFilename();

        if($value === 'null') {
            return null;
        }

        if($this->fieldType == ColumnType::STRING) {
            return (string)$value;

        } elseif ($this->fieldType == ColumnType::INTEGER) {
            return intval($value);

        } elseif ($this->fieldType == ColumnType::BOOLEAN) {
            return boolval($value);

        } elseif ($this->fieldType == ColumnType::FLOAT) {
            return floatval($value);

        } elseif ($this->fieldType == ColumnType::ARRAY) {
            throw new \LogicException("Cannot Index Arrays");
        }

        throw new \LogicException(sprintf("Unsupported Field Type: %s", $this->fieldType));
        
    }

    /**
     * Indexes a record
     * @param  RecordInterface $record record
     */
    public function indexRecord(RecordInterface $record): void
    {
        if($this->isRecordIndexed($record)){
            return;
        }

        $data = (string)$record->getId();
        file_put_contents(
            $this->file->getRealPath(), 
            $data . PHP_EOL, 
            FILE_APPEND | LOCK_EX
        );
    }

    public function clear(): void
    {
        $fs = new Filesystem();
        $fs->remove($this->file);
    }

    /**
     * Indicates if a record was indexed or not
     * @param  RecordInterface $record record
     * @return boolean                 true if indexed, otherwise false
     */
    public function isRecordIndexed(RecordInterface $record): bool
    {
        if(!$this->file->exists()) return false;

        $indexed = false;

        $handle = fopen($this->file->getRealPath(), "r");
        while (!feof($handle)) {
            $buffer = fgets($handle);
            if(strpos($buffer, (string)$record->getId()) !== false) {
                $indexed = true;
            }
        }
        fclose($handle);

        return $indexed;
    }

    /**
     * Returns a list of ids in the index file
     * @return array
     */
    public function getIds(): array
    {
        $content = $this->file->getContent();
        $lines = explode(PHP_EOL, $content);

        $lines = array_filter($lines, static function($l) {
            return $l !== '';
        });

        return array_map(static function ($l) {
            return trim($l);
        }, $lines);
    }

    /**
     * Returns the number of lines in an index file
     * @return int 
     */
    public function getNbIndexLines(): int
    {
        $lineCount = 0;
        
        $handle = fopen($this->file, "r");
        while(!feof($handle)){
            $line = fgets($handle, 8192);
            $lineCount = $lineCount + substr_count($line, PHP_EOL);
        }

        fclose($handle);

        return $lineCount;
    }

    public function __toString()
    {
        return (string)$this->file;
    }
}