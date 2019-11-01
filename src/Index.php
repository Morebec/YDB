<?php 

namespace Morebec\YDB;

use Morebec\ValueObjects\File\File;
use Morebec\YDB\Database\RecordInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Represents a database index
 */
class Index
{
    /** @var string name of the field that is indexed */
    private $fieldName;

    /** @var File */
    private $file;

    function __construct(string $fieldName, File $file)
    {
        $this->fieldName = $fieldName;
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
     * Indexes a record
     * @param  RecordInterface $record record
     */
    public function indexRecord(RecordInterface $record): void
    {
        return;
        
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
        
        $handle = fopen($file, "r");
        while(!feof($handle)){
            $line = fgets($handle, 8192);
            $lineCount = $lineCount + substr_count($line, PHP_EOL);
        }

        fclose($handle);

        return $lineCount;
    }
}