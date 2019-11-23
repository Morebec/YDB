<?php

namespace Morebec\YDB\CommandHandler\Database;

use Morebec\ValueObjects\File\Directory;
use Morebec\YDB\Command\Database\CreateDatabaseCommand;
use Morebec\YDB\Service\Database;
use Morebec\YDB\Event\Database\DatabaseCreatedEvent;
use Morebec\YDB\Exception\DatabaseException;
use Psr\Log\LogLevel;

/**
 * Handles the creation of a database
 */
class CreateDatabaseCommandHandler
{
    /** @var Database */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function __invoke(CreateDatabaseCommand $command)
    {
        $location = $this->database->getPath();

        // Check if the directory where the database is located actually exists
        $filesystem = $this->database->getFilesystem();
        if ($filesystem->exists($location)) {
            throw new DatabaseException(
                "Cannot create database at location $location: directory it already exists"
            );
        }

        // Create directories
        try {
            $filesystem->mkdir($location);
            $filesystem->mkdir("$location/" . Database::TABLES_DIR_NAME);
            $filesystem->mkdir("$location/" . Database::BIN_DIR_NAME);
            $filesystem->mkdir("$location/" . Database::LOGS_DIR_NAME);
        } catch (\Exception $e) {
            throw new DatabaseException(
                "Error while creating database structure '$location'. Reason: " . $e->getMessage()
            );
        }

        $this->database->dispatchEvent(DatabaseCreatedEvent::NAME, new DatabaseCreatedEvent());
    }
}
