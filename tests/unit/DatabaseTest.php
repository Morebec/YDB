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
    /** @var array Should contain created tables names */
    public $tableNames = [];

    /** @var string  contain a table name which should create test method*/
    public $tableName = '' ;

    public function _before()
    {
        $config = new DatabaseConfig(
            Directory::fromStringPath(__DIR__ . '/../_data/test-db')
        );
        $this->database = new Database($config);
        $this->generateRandomTableName();
    }

    public function _passed()
    {
        $this->database->delete();
    }
    
    public function testCreateTable(): void
    {
        $table = $this->database->createTable(
            new TableSchema($this->tableName, [
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
            new TableSchema($this->tableName, [
                new Column('id', ColumnType::STRING(), true),
                new Column('firstname', ColumnType::STRING()),
                new Column('lastname', ColumnType::STRING())
            ])
        );
        $this->assertTrue($this->database->tableExists($table));
        $this->generateRandomTableName();

        $newSchema = new TableSchema($this->tableName, [
                new Column('id', ColumnType::STRING(), true),
                new Column('a_column', ColumnType::STRING())
        ]);

        $table = $this->database->updateTable($table, $newSchema);

        $this->assertEquals($table->getSchema(), $newSchema);
    }

    public function testDeleteTable()
    {
        $table = $this->database->createTable(
            new TableSchema($this->tableName, [
                new Column('id', ColumnType::STRING(), true),
                new Column('firstname', ColumnType::STRING()),
                new Column('lastname', ColumnType::STRING())
            ])
        );

        $this->database->deleteTable($table);

        $this->assertFalse($this->database->tableExists($table));
    }

    public function generateRandomTableName(int $length = 10):void
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomTableName = '';
        for ($i = 0; $i < $length; $i++) {
            $randomTableName .= $characters[rand(0, $charactersLength - 1)];
        }
        if (in_array($randomTableName, $this->tableNames)) {
            $this->generateRandomTableName();
        }else{
            $this->tableNames[] = $randomTableName;
            $this->tableName = $randomTableName;
        }
    }
}