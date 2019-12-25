<?php

use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\Entity\Column;
use Morebec\YDB\Entity\RecordId;
use Morebec\YDB\Entity\Record;
use Morebec\YDB\Entity\TableSchema;
use Morebec\YDB\Enum\ColumnType;
use Morebec\YDB\Service\Database;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * DatabaseConnnectionTest
 */
class DatabaseConnectionTest extends TestCase
{
    public function testCreateDatabase(): void
    {
        $dbName = 'test-create-database';
        $dbPath = codecept_output_dir() . 'data/' . $dbName;
        $config = new DatabaseConfig($dbPath);
        $conn = Database::getConnection($config);

        $conn->createDatabase();

        $fs = new Filesystem();

        $this->assertTrue($fs->exists($dbPath));

        // Clean up
        $conn->deleteDatabase();
    }

    public function testDeleteDatabase(): void
    {
        $dbName = 'test-delete-database';
        $dbPath = codecept_output_dir() . 'data/' . $dbName;
        $config = new DatabaseConfig($dbPath);
        $conn = Database::getConnection($config);

        $conn->createDatabase();

        $fs = new Filesystem();

        $conn->deleteDatabase();
        $this->assertFalse($fs->exists($dbPath));    
    }

    public function testClearDatabase(): void
    {
        $dbName = 'test-clear-database';
        $dbPath = codecept_output_dir() . 'data/' . $dbName;
        $config = new DatabaseConfig($dbPath);
        $conn = Database::getConnection($config);

        $conn->createDatabase();

        $fs = new Filesystem();

        $conn->clearDatabase();

        // TODO: Complete

        // Cleanup
        $conn->deleteDatabase();

    }

    public function testCreateTable()
    {
        $dbName = 'test-create-table';
        $dbPath = codecept_output_dir() . 'data/' . $dbName;
        $config = new DatabaseConfig($dbPath);
        $conn = Database::getConnection($config);

        // Create DB
        $conn->createDatabase();

        // Create Table
        $dbName = 'test-table';
        $conn->createTable(new TableSchema($dbName, [
            new Column('field_1', ColumnType::STRING())
        ]));

        $fs = new Filesystem();

        $this->assertTrue($fs->exists($dbPath . '/tables/' . $dbName));

        // Cleanup
        $conn->deleteDatabase();
    }

    public function testInsertRecord(): void
    {
        $dbName = 'test-insert-record';
        $dbPath = codecept_output_dir() . 'data/' . $dbName;
        $config = new DatabaseConfig($dbPath);
        $conn = Database::getConnection($config);

        // Create DB
        $conn->createDatabase();

        // Create Table
        $dbName = 'test-table';
        $conn->createTable(new TableSchema($dbName, [
            new Column('field_1', ColumnType::STRING())
        ]));

        // Create record
        $record = new Record(RecordId::generate(), [
            'field_1' => 'value_of_field_1'
        ]);
        $conn->insertRecord($dbName, $record);

        $fs = new Filesystem();

        $recordId = $record->getId();

        $expectedPath = $dbPath . '/' . 
                        Database::TABLES_DIR_NAME . '/' . 
                        $dbName . '/' . 
                        $recordId . '.yaml';
            
        $this->assertTrue($fs->exists($expectedPath));

        $conn->deleteDatabase();
    }
}
