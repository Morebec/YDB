<?php

namespace Morebec\YDB\Domain\Command\Database;

use Morebec\YDB\Command\DatabaseCommandInterface;

/**
 * CreateDatabaseCommand
 */
class CreateDatabaseCommand implements DatabaseCommandInterface
{
    /**
     * @var string
     */
    private $location;

    /**
     * CreateDatabaseCommand constructor.
     * @param string $location
     */
    public function __construct(string $location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }
}
