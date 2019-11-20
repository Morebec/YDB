<?php 

namespace Morebec\YDB\CommandHandler\Database;

use Morebec\ValueObjects\File\Directory;
use Morebec\YDB\Command\Database\DeleteDatabaseCommand;
use Morebec\YDB\Exception\DatabaseException;
use Morebec\YDB\Service\Engine;

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
        $location = $this->engine->getDatabaseConfig()->getDatabasePath();
        $rootDirectory = Directory::fromStringPath($location);

        // Check if the directory where the databse is located actually exists
        if(!$rootDirectory->exists()) {
            throw new DatabaseException(
                "Cannot delete database at location $location: directory does not exists"
            );
        }

        // Exists, let's delete it        
        $filesystem = $this->engine->getFilesystem();

        try {
            $filesystem->remove($rootDirectory);
        } catch (\Exception $e) {
            throw new DatabaseException(
                "Error while deleting root directory at '$location'. Reason: " . $e->getMessage()
            );
        }
    }
}
