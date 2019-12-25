<?php


namespace Morebec\YDB\Infrastructure\Persistence;


use Morebec\YDB\Domain\Model\Entity\Database;
use Morebec\YDB\Domain\Model\Repository\DatabaseRepositoryInterface;
use Morebec\YDB\Exception\DatabaseException;
use Symfony\Component\Filesystem\Filesystem;

class DirectoryDatabaseRepository implements DatabaseRepositoryInterface
{
    public const TABLES_DIR_NAME = 'tables';

    public const BIN_DIR_NAME = 'bin';

    public const LOGS_DIR_NAME = 'logs';

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public function add(Database $database): void
    {
        // Check if the directory where the database is exists or not.
        // It expects that it does not
        $location = $database->getLocation();
        if ($this->filesystem->exists($location)) {
            throw new DatabaseException(
                "Cannot create database at location $location: directory it already exists"
            );
        }

        // Create Directories for tables, binaries and logs
        try {
            $this->filesystem->mkdir($location);
            $this->filesystem->mkdir("$location/" . self::TABLES_DIR_NAME);
            $this->filesystem->mkdir("$location/" . self::BIN_DIR_NAME);
            $this->filesystem->mkdir("$location/" . self::LOGS_DIR_NAME);
        } catch (\Exception $e) {
            throw new DatabaseException(
                "Error while creating database structure at '$location'. Reason: " . $e->getMessage()
            );
        }
    }

    public function remove(Database $database): void
    {

    }
}