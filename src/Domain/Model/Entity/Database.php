<?php


namespace Morebec\YDB\Domain\Model\Entity;


class Database
{
    /** @var string location of the database */
    private $location;

    public function __construct(string $location)
    {
        $this->location = $location;
    }

    /**
     * Creates an instance of a database object
     * @param string $location
     * @return static
     */
    public static function create(string $location): self
    {
        return new static($location);
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }
}