<?php 

use Morebec\YDB\CommandHandler\Database\ClearDatabaseCommandHandler;
use Morebec\YDB\Command\Database\ClearDatabaseCommand;
use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\Service\Engine;

/**
 * ClearDatabaseCommandHandlerTest
 */
class ClearDatabaseCommandHandlerTest extends \Codeception\Test\Unit
{
    public function testClearDatabase()
    {   
        $dbName = 'testCreateDatabase';
        $location = codecept_output_dir() . 'data/' . $dbName;
        $config = new DatabaseConfig($location);

        $engine = new Engine($config);
        $database = $engine->getDatabase();

        // TODO add a few records

        $handler = new ClearDatabaseCommandHandler($database);
        $command = new ClearDatabaseCommand();
        $handler($command);

        // TODO Assert that there are no more records
    }    
}