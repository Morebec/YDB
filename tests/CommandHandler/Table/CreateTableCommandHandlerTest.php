<?php

use Morebec\YDB\Command\Database\DeleteDatabaseCommand;
use Morebec\YDB\Command\Table\CreateTableCommand;
use Morebec\YDB\CommandHandler\Database\DeleteDatabaseCommandHandler;
use Morebec\YDB\CommandHandler\Table\CreateTableCommandHandler;
use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\Entity\Column;
use Morebec\YDB\Entity\TableSchema;
use Morebec\YDB\Enum\ColumnType;
use Morebec\YDB\Exception\TableAlreadyExistsException;
use Morebec\YDB\Service\Engine;
use PHPUnit\Framework\TestCase;

/**
 * CreateTableCommandHandlerTest
 */
class CreateTableCommandHandlerTest extends TestCase
{
    
    public function testCreateTable()
    {
        $dbName = 'test-create-table';
        $location = codecept_output_dir() . 'data/' . $dbName;
        $config = new DatabaseConfig($location);

        $engine = new Engine($config);
        $database = $engine->getDatabase();

        $tableName = 'test-table';

        $handler = new CreateTableCommandHandler($database);
        $command = new CreateTableCommand(new TableSchema($tableName, [
            new Column('field_1', ColumnType::STRING())
        ]));

        $handler($command);

        $this->assertTrue($database->tableExists($tableName));

        (new DeleteDatabaseCommandHandler($database))(new DeleteDatabaseCommand());
    }

    public function testCreateTableThatAlreadyExistsThrowsException()
    {
        // Create table once
        $dbName = 'test-create-table-that-already-exists-throws-exception';
        $location = codecept_output_dir() . 'data/' . $dbName;
        $config = new DatabaseConfig($location);

        $engine = new Engine($config);
        $database = $engine->getDatabase();

        $tableName = 'test-table-exception';

        $handler = new CreateTableCommandHandler($database);
        $command = new CreateTableCommand(new TableSchema($tableName, [
            new Column('field_1', ColumnType::STRING())
        ]));

        $handler($command);

        // Create table again, should throw exception
        $this->expectException(TableAlreadyExistsException::class);
        try {
            $handler($command);
        } catch (TableAlreadyExistsException $e) {
            (new DeleteDatabaseCommandHandler($database))(new DeleteDatabaseCommand());
            throw $e;
        }
    }    
}