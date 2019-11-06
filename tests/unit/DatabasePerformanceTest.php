<?php 

use Morebec\ValueObjects\File\Directory;
use Morebec\YDB\Column;
use Morebec\YDB\ColumnType;
use Morebec\YDB\Database;
use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\Operator;
use Morebec\YDB\QueryBuilder;
use Morebec\YDB\Record;
use Morebec\YDB\RecordId;
use Morebec\YDB\TableSchema;

/**
 * DatabasePerformanceTest
 */
class DatabasePerformanceTest extends \Codeception\Test\Unit
{
    
    public function _before()
    {
        $config = new DatabaseConfig(
            Directory::fromStringPath(__DIR__ . '/../_data/performance-test-db')
        );
        $this->database = new Database($config);

        $schema = new TableSchema('test-performance-table', [
            new Column('id', ColumnType::STRING(), true /* indexed */),
            new Column('first_name', ColumnType::STRING()),
            new Column('last_name', ColumnType::STRING()),
            new Column('age', ColumnType::INTEGER()),
            new Column('indexed_column', ColumnType::INTEGER(), true /* indexed */)
        ]);

        $this->database->createTable($schema);
    }

    public function _after()
    {
        $this->database->delete();
    }

    public function createData(int $nbRecords)
    {
        $table = $this->database->getTableByName('test-performance-table');
        for ($i=0; $i < $nbRecords; $i++) { 

            $record = new Record(
                RecordId::generate(),
                [
                    'first_name' => 'John ' . $i,
                    'last_name' => 'Doe',
                    'age' => $i,
                    'indexed_column' => $i
                ]
            );

            $table->addRecord($record);
        }
    }

    public function testRecordCreation()
    {
        $t = time();
        $this->createData(100);
        $t2 = time();
        
        $delta = $t2 - $t;

        $this->assertTrue($delta <= 1);
    }

    public function testQueryAll()
    {
        $this->createData(200);

        $table = $this->database->getTableByName('test-performance-table');

        $t = time();
        $allRecords = $table->queryAll(); 
        $t2 = time();
        
        $delta = $t2 - $t;

        $this->assertTrue($delta <= 1);
    }

    public function testQueryOneNotIndexed()
    {
        $this->createData(150);

        $table = $this->database->getTableByName('test-performance-table');

        $t = time();
        $allRecords = $table->queryOne(QueryBuilder::find('age', Operator::EQUAL(), 99)->build()); 
        $t2 = time();
        
        $delta = $t2 - $t;
        $this->assertTrue($delta <= 1);
    }

    public function testQueryOneIndexed()
    {
        $this->createData(150);

        $table = $this->database->getTableByName('test-performance-table');

        $t = time();
        $allRecords = $table->queryOne(
            QueryBuilder::find('indexed_column', Operator::EQUAL(), 99)->build()
        ); 
        $t2 = time();
        
        $delta = $t2 - $t;
        $this->assertTrue($delta <= 1);
    }
}