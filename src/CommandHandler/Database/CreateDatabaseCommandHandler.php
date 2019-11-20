<?php 

namespace Morebec\YDB\CommandHandler\Database;

use Morebec\ValueObjects\File\Directory;
use Morebec\YDB\Command\Database\CreateDatabaseCommand;
use Morebec\YDB\Database;
use Morebec\YDB\Exception\DatabaseException;
use Morebec\YDB\Service\Engine;
use Psr\Log\LogLevel;

/**
 * Handles the creation of a database
 */
class CreateDatabaseCommandHandler
{
    /** @var Engine */
    private $engine;

    function __construct(Engine $engine)
    {
        $this->engine = $engine;   
    }

    public function __invoke(CreateDatabaseCommand $command)
    {
        $location = $this->engine->getDatabaseConfig()->getDatabasePath();

        // Check if the directory where the databse is located actually exists
        $filesystem = $this->engine->getFilesystem();
        if($filesystem->exists($location)) {
            throw new DatabaseException(
                "Cannot create database at location $location: directory it already exists"
            );
        }

        // Create directories
        try {
            $filesystem->mkdir($location);
            $filesystem->mkdir("$location/" . Database::TABLE_DIR_NAME);
            $filesystem->mkdir("$location/" . Database::BIN_DIR_NAME);
            $filesystem->mkdir("$location/" . Database::LOGS_DIR_NAME);
        } catch (\Exception $e) {
            throw new DatabaseException(
                "Error while creating database structure '$location'. Reason: " . $e->getMessage()
            );
        }

        $this->engine->log(LogLevel::INFO, 'Database created');
    }
}
