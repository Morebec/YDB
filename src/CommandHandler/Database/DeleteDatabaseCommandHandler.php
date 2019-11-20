<?php 

namespace Morebec\YDB\CommandHandler\Database;

use Morebec\ValueObjects\File\Directory;
use Morebec\YDB\Command\Database\DeleteDatabaseCommand;
use Morebec\YDB\Exception\DatabaseException;
use Morebec\YDB\Service\Engine;
use Psr\Log\LogLevel;

/**
 * Handles the deletion of a database
 */
class DeleteDatabaseCommandHandler
{
    /** @var Engine */
    private $engine;

    function __construct(Engine $engine)
    {
        $this->engine = $engine;   
    }

    public function __invoke(DeleteDatabaseCommand $command)
    {
        $this->engine->log(LogLevel::INFO, 'Command Requested: Delete database');

        $location = $this->engine->getDatabaseConfig()->getDatabasePath();

        // Check if the directory where the databse is located actually exists
        $filesystem = $this->engine->getFilesystem();
        
        if(!$filesystem->exists($location)) {
            throw new DatabaseException(
                "Cannot delete database at location $location: directory does not exists"
            );
        }

        // Exists, let's delete it     
        $filesystem = $this->engine->getFilesystem();

        try {
            $this->engine->log(LogLevel::INFO, 'Deleting database ...');
            $filesystem->remove($location);
        } catch (\Exception $e) {
            throw new DatabaseException(
                "Error while deleting root directory at '$location'. Reason: " . $e->getMessage()
            );
        }
    }
}
