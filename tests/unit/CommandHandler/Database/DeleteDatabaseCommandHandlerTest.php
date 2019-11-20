<?php 

use Morebec\ValueObjects\File\Directory;
use Morebec\YDB\CommandHandler\Database\CreateDatabaseCommandHandler;
use Morebec\YDB\CommandHandler\Database\DeleteDatabaseCommandHandler;
use Morebec\YDB\Command\Database\CreateDatabaseCommand;
use Morebec\YDB\Command\Database\DeleteDatabaseCommand;
use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\Exception\DatabaseException;
use Morebec\YDB\Exception\DatabaseNotFoundException;
use Morebec\YDB\Service\Engine;

/**
 * DeleteDatabaseCommandHandlerTest
 */
class DeleteDatabaseCommandHandlerTest extends \Codeception\Test\Unit
{
    public function testDeleteDatabase(): void
    {
        $dbName = 'testDeleteDatabase';
        $location = codecept_output_dir() . 'data/' .$dbName;
        $config = new DatabaseConfig($location);

        $engine = new Engine($config);

        // Create the database
        $handler = new CreateDatabaseCommandHandler($engine);
        $handler(new CreateDatabaseCommand());

        // Delete the database
        $handler = new DeleteDatabaseCommandHandler($engine);
        $handler(new DeleteDatabaseCommand());

        $fs = $engine->getFilesystem();

        $this->assertFalse(
            $fs->exists($location), 
            'Failed asserting the database directory was succesfully deleted.'
        );
    }

    public function testDeleteDatabaseWhenItDoesNotExistsThrowsException(): void
    {
        $dbName = 'testDeleteDatabaseWhenItDoesNotExistsThrowsException';
        $config = new DatabaseConfig(codecept_output_dir() . 'data/' .$dbName);

        $engine = new Engine($config);


        $handler = new DeleteDatabaseCommandHandler($engine);

        // The database does not exist
        $this->expectException(DatabaseNotFoundException::class);
        $handler(new DeleteDatabaseCommand());
    }
}