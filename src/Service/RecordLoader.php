<?php

namespace Morebec\YDB\Service;

use Assert\Assert;
use Assert\Assertion;
use Morebec\ValueObjects\File\File;
use Morebec\YDB\Contract\RecordInterface;
use Morebec\YDB\Entity\Identity\RecordId;
use Morebec\YDB\Entity\Record;
use Symfony\Component\Yaml\Yaml;

/**
 * Loads records from file
 */
class RecordLoader
{
    public function __construct()
    {
    }

    /**
     * Tries to load a record from file
     * @param  File   $recordFile record file
     * @return RecordInterface    loaded record
     */
    public function load(File $recordFile): RecordInterface
    {
        Assertion::true(
            $recordFile->exists(),
            "Cannot load record, file '$recordFile' does not exist."
        );

        $content = $recordFile->getContent();
        $data = Yaml::parse($content);

        Assertion::keyExists($data, 'id');
        Assertion::notNull($data['id']);
        $r = new Record(
            new RecordId($data['id']),
            $data
        );

        return $r;
    }
}
