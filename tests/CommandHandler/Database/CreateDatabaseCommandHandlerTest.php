<?php

use Morebec\YDB\Command\Database\CreateDatabaseCommand;
use Morebec\YDB\Command\Database\DeleteDatabaseCommand;
use Morebec\YDB\CommandHandler\Database\CreateDatabaseCommandHandler;
use Morebec\YDB\CommandHandler\Database\DeleteDatabaseCommandHandler;
use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\Exception\DatabaseException;
use Morebec\YDB\Service\Engine;
use PHPUnit\Framework\TestCase;

/**
 * CreateDatabaseCommandHandlerTest
 */
class CreateDatabaseCommandHandlerTest extends TestCase
{
    public function testCreateDatabase(): void
    {
        $dbName = 'testCreateDatabase';
        $location = codecept_output_dir() . 'data/' . $dbName;
        $config = new DatabaseConfig($location);

        $engine = new Engine($config);
        $database = $engine->getDatabase();
        
        $handler = new CreateDatabaseCommandHandler($database);

        $handler(new CreateDatabaseCommand());

        $fs = $database->getFilesystem();
        $this->assertTrue($fs->exists($location));

        // Clean delete database
        $handler = new DeleteDatabaseCommandHandler($database);
        $handler(new DeleteDatabaseCommand());
    }
     
    public function testCreateDatabaseWhenItAlreadyExistsThrowsException(): void
    {
        $dbName = 'testCreateDatabaseWhenItAlreadyExistsThrowsException';
        $config = new DatabaseConfig(codecept_output_dir() . 'data/' . $dbName);

        $engine = new Engine($config);
        $database = $engine->getDatabase();

        $handler = new CreateDatabaseCommandHandler($database);
        $handler(new CreateDatabaseCommand());

        // At this point it should already exist
        $this->expectException(DatabaseException::class);
        try {
            $handler(new CreateDatabaseCommand());
        } catch (DatabaseException $e) {
            // Clean up and rethrow for PHPUnit
            $handler = new DeleteDatabaseCommandHandler($database);
            $handler(new DeleteDatabaseCommand());
            throw new $e;
        }
    }
}