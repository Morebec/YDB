<?php

namespace Morebec\YDB\Domain\CommandHandler\Database;

use Morebec\YDB\Command\Database\CreateDatabaseCommand;
use Morebec\YDB\Domain\Model\Entity\Database;
use Morebec\YDB\Domain\Model\Repository\DatabaseRepositoryInterface;
use Morebec\YDB\Event\Database\DatabaseCreatedEvent;

/**
 * Handles the creation of a database
 */
class CreateDatabaseCommandHandler
{
    /** @var Database */
    private $databaseRepository;

    public function __construct(
        DatabaseRepositoryInterface $database
    )
    {
        $this->databaseRepository = $database;
    }

    public function __invoke(CreateDatabaseCommand $command)
    {
        $location = $command->getLocation();

        $database = Database::create($location);
        $this->databaseRepository->add($database->setLocation($location));

        $this->databaseRepository->dispatchEvent(DatabaseCreatedEvent::NAME, new DatabaseCreatedEvent());
    }
}
