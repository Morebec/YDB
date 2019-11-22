<?php

namespace Morebec\YDB\CommandHandler\Database;

use Morebec\ValueObjects\File\Directory;
use Morebec\YDB\Command\Database\DeleteDatabaseCommand;
use Morebec\YDB\Event\Database\DatabaseDeletedEvent;
use Morebec\YDB\Exception\DatabaseException;
use Morebec\YDB\Exception\DatabaseNotFoundException;
use Morebec\YDB\Service\Database;
use Psr\Log\LogLevel;

/**
 * Handles the deletion of a database
 */
class DeleteDatabaseCommandHandler
{
    /** @var Database */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function __invoke(DeleteDatabaseCommand $command)
    {
        $this->database->log(LogLevel::INFO, 'Command Requested: Delete database');

        $location = $this->database->getPath();

        // Check if the directory where the database is located actually exists
        $filesystem = $this->database->getFilesystem();
        
        if (!$filesystem->exists($location)) {
            throw new DatabaseNotFoundException(
                "Cannot delete database at location $location: directory does not exists"
            );
        }

        // Exists, let's delete it
        $filesystem = $this->database->getFilesystem();

        try {
            $this->database->log(LogLevel::INFO, 'Deleting database ...');
            $filesystem->remove($location);
        } catch (\Exception $e) {
            throw new DatabaseException(
                "Error while deleting root directory at '$location'. Reason: " . $e->getMessage()
            );
        }

        $this->database->dispatchEvent(DatabaseDeletedEvent::NAME, new DatabaseDeletedEvent());
    }
}
