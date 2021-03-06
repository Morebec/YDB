<?php 

use Morebec\ValueObjects\File\Directory;
use Morebec\ValueObjects\File\Path;
use Morebec\YDB\Column;
use Morebec\YDB\ColumnType;
use Morebec\YDB\Criteria;
use Morebec\YDB\Criterion;
use Morebec\YDB\Database;
use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\Operator;
use Morebec\YDB\Query;
use Morebec\YDB\QueryBuilder;
use Morebec\YDB\Record;
use Morebec\YDB\RecordId;
use Morebec\YDB\TableSchema;

/**
 * TableTest
 */
class TableTest extends \Codeception\Test\Unit
{
    public function _before()
    {
        $config = new DatabaseConfig(
            Directory::fromStringPath(__DIR__ . '/../_data/test-db')
        );
        $this->database = new Database($config);
    }

    public function _after()
    {
       // $this->database->delete();
    }
    
    public function testCreateRecord(): void
    {
        $table = $this->createTestTable('test-create-record');

        $record = new Record(
            RecordId::generate(),
            [
                'first_name' => 'James',
                'last_name' => 'Bond',
                'age' => 39
            ]
        );
        $table->addRecord($record);

        $r = $table->queryOne(
            Query::findByField('first_name', Operator::STRICTLY_EQUAL(), 'James')
        );

        $this->assertNotNull($r);
        $this->assertTrue($r->isEqualTo($record));
    }

    public function testUpdateRecord()
    {
        $table = $this->createTestTable('test-update-record');

        $record = new Record(
            RecordId::generate(),
            [
                'first_name' => 'James',
                'last_name' => 'Bond',
                'age' => 39
            ]
        );

        $table->addRecord($record);

        $record->setFieldValue('first_name', 'Barney');
        $record->setFieldValue('last_name', 'Stinson');

        $table->updateRecord($record);
        
        $r = $table->queryOne(Query::findById($record->getId()));

        $this->assertNotNull($r);
        $this->assertEquals('Barney', $r->getFieldValue('first_name'));
    }

    public function testDeleteRecord()
    {
        $table = $this->createTestTable('test-delete-record');

        $record = new Record(
            RecordId::generate(),
            [
                'first_name' => 'James',
                'last_name' => 'Bond',
                'age' => 39
            ]
        );

        $table->addRecord($record);

        $table->deleteRecord($record);

        $r = $table->queryOne(Query::findById($record->getId()));
        $this->assertNull($r);
    }

    public function testQueryRecordMultipleCriteria()
    {
        $table = $this->createTestTable('test-query-record-multiple-criteria');

        $record = new Record(
            RecordId::generate(),
            [
                'first_name' => 'James',
                'last_name' => 'Bond',
                'age' => 42
            ]
        );

        $table->addRecord($record);


        $r = $table->queryOne(
            new Query([
                new Criterion('first_name', Operator::STRICTLY_EQUAL(), 'James'),
                new Criterion('last_name', Operator::STRICTLY_EQUAL(), 'Bond'),
                new Criterion('age', Operator::GREATER_OR_EQUAL(), 42)
            ])
        );

        $this->assertNotNull($r);
        $this->assertTrue($r->isEqualTo($record));
    }


    public function testAndCriteriaOnly()
    {
        # code...
    }

    public function testOrCriteriaOnly($value='')
    {
        # code...
    }

    public function testComplexQueryWithQueryBuilder()
    {
        $table = $this->createTestTable('test-complex-query-with-query-builder');

        $table->addRecord(new Record(
                RecordId::generate(),
                [
                    'first_name' => 'James',
                    'last_name' => 'Bond',
                    'age' => 42
                ]
            )
        );

        $table->addRecord(new Record(
                RecordId::generate(),
                [
                    'first_name' => 'Barney',
                    'last_name' => 'Stinson',
                    'age' => 31
                ]
            )
        );

        $query = new Query([
                new Criterion('first_name', Operator::STRICTLY_EQUAL(), 'James'),
                new Criterion('last_name', Operator::STRICTLY_EQUAL(), 'Bond'),
            ],
            [
                new Criterion('age', Operator::GREATER_OR_EQUAL(), 25)
            ]
        );

        $qbQuery = QueryBuilder::find('first_name', Operator::STRICTLY_EQUAL(), 'James')
                             ->and('last_name', Operator::STRICTLY_EQUAL(), 'Bond')
                             ->or('age', Operator::GREATER_OR_EQUAL(), 25)
                             ->build()
        ;

        $this->assertEquals((string)$query, (string)$qbQuery);
        $this->assertTrue($query->isEqualTo($qbQuery));



        $r = $table->query($query);
        $this->assertNotNull($r);

        if(count($r) !== 2) {
            codecept_debug($r);
            codecept_debug(iterator_to_array($table->queryAll()));
        }

        $this->assertCount(2, $r);
    }

    public function testAddColumn()
    {
        $table = $this->createTestTable('test-add-column');

        $record = new Record(
            RecordId::generate(),
            [
                'first_name' => 'James',
                'last_name' => 'Bond',
                'age' => 42
            ]
        );

        $table->addRecord($record);

        $table->addColumn(new Column('email', ColumnType::STRING()), 'user@email.com');

        $r = $table->queryOne(
            QueryBuilder::find('email', Operator::STRICTLY_EQUAL(), 'user@email.com')
                        ->build()
        );

        $this->assertNotNull($r);
    }

    public function testUpdateColumn()
    {
        $table = $this->createTestTable('test-update-column');

        $record = new Record(
            RecordId::generate(),
            [
                'first_name' => 'James',
                'last_name' => 'Bond',
                'age' => 42
            ]
        );

        $table->addRecord($record);

        $table->deleteColumn($table->getColumnByName('last_name'));
        
        $table->updateColumn(
            $table->getColumnByName('first_name'), 
            new Column('fullname', ColumnType::STRING())
        );

        $r = $table->queryOne(
            QueryBuilder::find('fullname', Operator::STRICTLY_EQUAL(), 'James')
                        ->build()
        );

        $this->assertNotNull($r);
    }

    public function testDeleteColumn()
    {
        $table = $this->createTestTable('test-delete-column');

        $record = new Record(
            RecordId::generate(),
            [
                'first_name' => 'James',
                'last_name' => 'Bond',
                'age' => 42
            ]
        );

        $table->addRecord($record);

        $table->deleteColumn($table->getColumnByName('age'));

        $r = $table->queryOne(
            QueryBuilder::find('age', Operator::STRICTLY_EQUAL(), 42)
                        ->build()
        );

        $this->assertNull($r);
    }

    protected function createTestTable(string $tableName,array $additionalColumns = []):Morebec\YDB\Table
    {
        $baseColumns = [
            new Column('id', ColumnType::STRING(), true),
            new Column('first_name', ColumnType::STRING()),
            new Column('last_name', ColumnType::STRING()),
            new Column('age', ColumnType::INTEGER())
        ];

        $columns = array_merge($baseColumns,$additionalColumns);

        return $this->database->createTable(
            new TableSchema($tableName,$columns)
        );
    }
}