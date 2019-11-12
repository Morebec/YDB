<?php 

use Morebec\ValueObjects\File\Directory;
use Morebec\ValueObjects\File\Path;
use Morebec\YDB\Column;
use Morebec\YDB\ColumnType;
use Morebec\YDB\Database;
use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\TableSchema;

/**
 * DatabaseTest
 */
class DatabaseTest extends \Codeception\Test\Unit
{
    public function _before()
    {
        $config = new DatabaseConfig(
            Directory::fromStringPath(__DIR__ . '/../_data/test-db')
        );
        $this->database = new Database($config);
    }

    public function _passed()
    {
        $this->database->delete();
    }
    
    public function testCreateTable(): void
    {
        $table = $this->createTestTable('test-create-table');

        $this->assertTrue($this->database->tableExists($table));
    }

    public function testUpdateTable()
    {
        $table = $this->createTestTable('test-update-table');

        $this->assertTrue($this->database->tableExists($table));

        $newSchema = new TableSchema('test-update-table-with-new-name', [
                new Column('id', ColumnType::STRING(), true),
                new Column('a_column', ColumnType::STRING())
        ]);

        $table = $this->database->updateTable($table, $newSchema);
        
        $this->assertEquals($table->getSchema(), $newSchema);
    }

    public function testDeleteTable()
    {
        $table = $this->createTestTable('test-update-table');

        $this->database->deleteTable($table);

        $this->assertFalse($this->database->tableExists($table));
    }

    protected function createTestTable(string $tableName):Morebec\YDB\Table
    {
        return $this->database->createTable(
            new TableSchema($tableName,[
                new Column('id', ColumnType::STRING(), true),
                new Column('firstname', ColumnType::STRING()),
                new Column('lastname', ColumnType::STRING())

            ])
        );
    }
}