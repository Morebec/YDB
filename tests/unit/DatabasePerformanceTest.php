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
 * @group performance
 */
class DatabasePerformanceTest extends \Codeception\Test\Unit
{
    
    public function _before()
    {
        $config = new DatabaseConfig(
            Directory::fromStringPath(__DIR__ . '/../_data/performance-test-db')
        );
        $this->database = new Database($config);
    }

    public function _passed()
    {
        $this->database->delete();
    }

    public function createData(string $tableName, int $nbRecords): void
    {
        $schema = new TableSchema($tableName, [
            new Column('id', ColumnType::STRING(), true /* indexed */),
            new Column('first_name', ColumnType::STRING()),
            new Column('last_name', ColumnType::STRING()),
            new Column('age', ColumnType::INTEGER()),
            new Column('indexed_column', ColumnType::INTEGER(), true /* indexed */)
        ]);

        $table = $this->database->createTable($schema);

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
        $this->createData('test-create-records', 1000);
        $t2 = time();
        
        $delta = $t2 - $t;

        $this->assertLessThanOrEqual(1.5, $delta);
    }

    public function testQueryAll()
    {
        $this->createData('test-query-all', 1000);

        $table = $this->database->getTableByName('test-query-all');

        $t = time();
        $allRecords = $table->queryAll(); 
        $t2 = time();
        
        $delta = $t2 - $t;

        $this->assertLessThanOrEqual(1, $delta);
    }

    public function testQueryOneNotIndexed()
    {
        $this->createData('test-query-not-indexed', 1000);

        $table = $this->database->getTableByName('test-query-not-indexed');

        $t = time();
        $allRecords = $table->queryOne(QueryBuilder::find('age', Operator::EQUAL(), 99)->build()); 
        $t2 = time();
        
        $delta = $t2 - $t;
        $this->assertLessThanOrEqual(1, $delta);
    }

    public function testQueryOneIndexed()
    {
        $this->createData('test-query-indexed', 1000);

        $table = $this->database->getTableByName('test-query-indexed');

        $t = time();
        $allRecords = $table->queryOne(
            QueryBuilder::find('indexed_column', Operator::EQUAL(), 99)->build()
        ); 
        $t2 = time();
        
        $delta = $t2 - $t;
        $this->assertLessThanOrEqual(1, $delta);
    }
}