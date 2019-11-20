<?php 

use Morebec\ValueObjects\File\Directory;
use Morebec\YDB\CommandHandler\Database\CreateDatabaseCommandHandler;
use Morebec\YDB\CommandHandler\Database\DeleteDatabaseCommandHandler;
use Morebec\YDB\Command\Database\CreateDatabaseCommand;
use Morebec\YDB\Command\Database\DeleteDatabaseCommand;
use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\Exception\DatabaseException;
use Morebec\YDB\Service\Engine;

/**
 * CreateDatabaseCommandHandlerTest
 */
class CreateDatabaseCommandHandlerTest extends \Codeception\Test\Unit
{
    public function testCreateDatabase(): void
    {
        $dbName = 'testCreateDatabaseWhenItAlreadyExistsThrowsException';
        $location = __DIR__ . codecept_output_dir() . 'data/' . $dbName;
        $config = new DatabaseConfig($location);

        $engine = new Engine($config);
        $handler = new CreateDatabaseCommandHandler($engine);

        $handler(new CreateDatabaseCommand());

        $fs = $engine->getFilesystem();
        $this->assertTrue($fs->exists($location));
    }
     
    public function testCreateDatabaseWhenItAlreadyExistsThrowsException(): void
    {
        $dbName = 'testCreateDatabaseWhenItAlreadyExistsThrowsException';
        $config = new DatabaseConfig(__DIR__ . codecept_output_dir() . 'data/' . $dbName);

        $engine = new Engine($config);
        $handler = new CreateDatabaseCommandHandler($engine);

        $handler(new CreateDatabaseCommand());

        // At this point it should already exist
        $this->expectException(DatabaseException::class);
        try {
            //$handler(new CreateDatabaseCommand());
        } catch (DatabaseException $e) {
            // Clean up and rethrow for PHPUnit
            $handler = new DeleteDatabaseCommandHandler($engine);
            $handler(new DeleteDatabaseCommand());
            throw new $e;
        }
    }
}