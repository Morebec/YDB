<?php 

use Morebec\ValueObjects\File\Directory;
use Morebec\ValueObjects\File\Path;
use Morebec\YDB\Column;
use Morebec\YDB\ColumnType;
use Morebec\YDB\Database;
use Morebec\YDB\TableSchema;

/**
 * DatabaseTest
 */
class DatabaseTest extends \Codeception\Test\Unit
{
    public function _before()
    {
        $this->database = new Database(
            Directory::fromStringPath(__DIR__ . '/../_data/test-db')
        );
    }

    public function _after()
    {
        $this->database->delete();
    }
    
    public function testCreateTable(): void
    {
        $table = $this->database->createTable(
            new TableSchema('test-create-table', [
                new Column('id', ColumnType::STRING(), true),
                new Column('firstname', ColumnType::STRING()),
                new Column('lastname', ColumnType::STRING())
            ])
        );
        $this->assertTrue($this->database->tableExists($table));
    }

    public function testUpdateTable()
    {
        $table = $this->database->createTable(
            new TableSchema('test-update-table', [
                new Column('id', ColumnType::STRING(), true),
                new Column('firstname', ColumnType::STRING()),
                new Column('lastname', ColumnType::STRING())
            ])
        );
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
        $table = $this->database->createTable(
            new TableSchema('test-update-table', [
                new Column('id', ColumnType::STRING(), true),
                new Column('firstname', ColumnType::STRING()),
                new Column('lastname', ColumnType::STRING())
            ])
        );

        $this->database->deleteTable($table);

        $this->assertFalse($this->database->tableExists($table));
    }
}