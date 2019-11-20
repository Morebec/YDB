<?php 

use Morebec\YDB\CommandHandler\Database\DeleteDatabaseCommandHandler;
use Morebec\YDB\CommandHandler\Table\CreateTableCommandHandler;
use Morebec\YDB\Command\Database\DeleteDatabaseCommand;
use Morebec\YDB\Command\Table\CreateTableCommand;
use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\Entity\Column;
use Morebec\YDB\Entity\TableSchema;
use Morebec\YDB\Enum\ColumnType;
use Morebec\YDB\Exception\TableAlreadyExistsException;
use Morebec\YDB\Service\Engine;

/**
 * CreateTableCommandHandlerTest
 */
class CreateTableCommandHandlerTest extends \Codeception\Test\Unit
{
    
    public function testCreateTable()
    {
        $dbName = 'testCreateTable';
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
        $dbName = 'testCreateTable';
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
        $handler($command);

        (new DeleteDatabaseCommandHandler($database))(new DeleteDatabaseCommand());
    }    
}