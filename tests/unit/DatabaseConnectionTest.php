<?php 

use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\Service\Database;
use Symfony\Component\Filesystem\Filesystem;

/**
 * DatabaseConnnectionTest
 */
class DatabaseConnectionTest extends \Codeception\Test\Unit
{
    public function testCreateDatabase()
    {
        $dbName = 'create-database';
        $dbPath = codecept_output_dir() . 'data/' . $dbName;
        $config = new DatabaseConfig($dbPath);
        $conn = Database::getConnection($config);

        $conn->createDatabase();

        $fs = new Filesystem();

        $this->assertTrue($fs->exists($dbPath));

        // Clean up
        $conn->deleteDatabase();
    }

    public function testDeleteDatabase()
    {
        $dbName = 'delete-database';
        $dbPath = codecept_output_dir() . 'data/' . $dbName;
        $config = new DatabaseConfig($dbPath);
        $conn = Database::getConnection($config);

        $conn->createDatabase();

        $fs = new Filesystem();

        $conn->deleteDatabase();
        $this->assertFalse($fs->exists($dbPath));    
    }

    public function testClearDatabase()
    {
        $dbName = 'delete-database';
        $dbPath = codecept_output_dir() . 'data/' . $dbName;
        $config = new DatabaseConfig($dbPath);
        $conn = Database::getConnection($config);

        $conn->createDatabase();

        $fs = new Filesystem();

        $conn->clearDatabase();

        // TODO: Complete
    }
}
