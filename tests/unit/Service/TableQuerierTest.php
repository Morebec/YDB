<?php 

use Morebec\YDB\ColumnBuilder;
use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\Entity\Identity\RecordId;
use Morebec\YDB\Entity\Query\Operator;
use Morebec\YDB\Entity\Record;
use Morebec\YDB\Enum\ColumnType;
use Morebec\YDB\QueryBuilder;
use Morebec\YDB\Service\Database;
use Morebec\YDB\TableSchemaBuilder;

/**
 * TableQuerierTest
 */
class TableQuerierTest extends \Codeception\Test\Unit
{
    
    public function testQueryTable()
    {
        $dbName = 'test-query-table';
        $dbPath = codecept_output_dir() . 'data/' . $dbName;
        $config = new DatabaseConfig($dbPath);
        $conn = Database::getConnection($config);

        $conn->createDatabase();

        $tableName = 'test-table';
        $schema = TableSchemaBuilder::withName($tableName)
                ->withColumn(
                    ColumnBuilder::withName('field_1')
                                 ->withType(ColumnType::STRING())
                                 ->build()
                )
                ->build()
        ;

        $conn->createTable($schema);

        $record = new Record(RecordId::generate(), [
                    'field_1' => 'value_1'
        ]);
        
        $conn->insertRecord($tableName, $record);

        $query = QueryBuilder::where('field_1', Operator::EQUAL(), 'value_1')->build();

        $result = $conn->query($tableName, $query);

        $this->assertEquals((string)$record, (string)$result->fetch());

        $conn->deleteDatabase();
    }

    public function testQueryWithMultipleCriteria()
    {

        $dbName = 'test-query-table-multiple-criteria';
        $dbPath = codecept_output_dir() . 'data/' . $dbName;
        $config = new DatabaseConfig($dbPath);
        $conn = Database::getConnection($config);

        $conn->createDatabase();

        $tableName = 'books';
        $schema = TableSchemaBuilder::withName($tableName)
                ->withColumn(
                    ColumnBuilder::withName('price')
                                 ->withFloatType()
                                 ->indexed()
                                 ->build()
                )
                ->withColumn(
                    ColumnBuilder::withName('genre')
                                 ->withStringType()
                                 ->indexed()
                                 ->build()
                )
                ->build()
        ;

        $conn->createTable($schema);

        
        $conn->insertRecord($tableName, new Record(RecordId::generate(), [
            'price' => 2.00,
            'genre' => 'adventure',
        ]));
        $conn->insertRecord($tableName, new Record(RecordId::generate(), [
            'price' => 5.00,
            'genre' => 'sci-fi',
        ]));
        $conn->insertRecord($tableName, new Record(RecordId::generate(), [
            'price' => 10.00,
            'genre' => 'fantasy',
        ]));

        $query = QueryBuilder::where('price', Operator::EQUAL(), 2.00)
                             ->orWhere('genre', Operator::EQUAL(), 'fantasy')
                             ->build()
        ;

        $result = $conn->query($tableName, $query);
        $records = $result->fetchAll();
        $this->assertCount(2, $records);
        $conn->deleteDatabase();
    } 

    public function testQueryOneInABigTable()
    {

        $dbName = 'test-query-one-in-big-table';
        $dbPath = codecept_output_dir() . 'data/' . $dbName;
        $config = new DatabaseConfig($dbPath);
        $conn = Database::getConnection($config);

        $conn->createDatabase();

        $tableName = 'books';
        $schema = TableSchemaBuilder::withName($tableName)
                ->withColumn(
                    ColumnBuilder::withName('price')
                                 ->withFloatType()
                                 ->indexed()
                                 ->build()
                )
                ->build()
        ;

        $conn->createTable($schema);

        $recordId = RecordId::generate();
        $conn->insertRecord($tableName, new Record($recordId, [
            'price' => 2.00,
        ]));

        for ($i=0; $i < 100; $i++) { 
            $conn->insertRecord($tableName, new Record(RecordId::generate(), [
                'price' => 2.00,
            ]));
        }

        $query = QueryBuilder::where('id', Operator::EQUAL(), $recordId)
                             ->build()
        ;

        $result = $conn->query($tableName, $query);
        $records = $result->fetchAll();
        $this->assertCount(1, $records);
        // $conn->deleteDatabase();
    }
}