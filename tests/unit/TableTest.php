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
        $table = $this->database->createTable(
            new TableSchema('test-create-record', [
                new Column('id', ColumnType::STRING()),
                new Column('first_name', ColumnType::STRING()),
                new Column('last_name', ColumnType::STRING()),
                new Column('age', ColumnType::INTEGER(), true)
            ])
        );

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
        $table = $this->database->createTable(
            new TableSchema('test-update-record', [
                new Column('id', ColumnType::STRING(), true),
                new Column('first_name', ColumnType::STRING()),
                new Column('last_name', ColumnType::STRING())
            ])
        );

        $record = new Record(
            RecordId::generate(),
            [
                'first_name' => 'James',
                'last_name' => 'Bond'
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
        $table = $this->database->createTable(
            new TableSchema('test-delete-record', [
                new Column('id', ColumnType::STRING(), true),
                new Column('first_name', ColumnType::STRING()),
                new Column('last_name', ColumnType::STRING())
            ])
        );

        $record = new Record(
            RecordId::generate(),
            [
                'first_name' => 'James',
                'last_name' => 'Bond'
            ]
        );

        $table->addRecord($record);

        $table->deleteRecord($record->getId());

        $r = $table->queryOne(Query::findById($record->getId()));
        $this->assertNull($r);
    }

    public function testQueryRecordMultipleCriteria($value='')
    {
        $table = $this->database->createTable(
            new TableSchema('test-query-multiple-record', [
                new Column('id', ColumnType::STRING(), true),
                new Column('first_name', ColumnType::STRING()),
                new Column('last_name', ColumnType::STRING()),
                new Column('age', ColumnType::INTEGER())
            ])
        );

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

    public function testComplexQueryWithQueryBuilder()
    {
        $table = $this->database->createTable(
            new TableSchema('test-query-builder', [
                new Column('id', ColumnType::STRING(), true),
                new Column('first_name', ColumnType::STRING()),
                new Column('last_name', ColumnType::STRING()),
                new Column('age', ColumnType::INTEGER())
            ])
        );

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

        $query = QueryBuilder::find('first_name', Operator::STRICTLY_EQUAL(), 'James')
                             ->and('last_name', Operator::STRICTLY_EQUAL(), 'Bond')
                             ->or('age', Operator::GREATER_OR_EQUAL(), 25)
                             ->build()
        ;
        $r = $table->query($query);

        $this->assertNotNull($r);
        $this->assertCount(2, $r);
    }

    public function testAddColumn()
    {
        $table = $this->database->createTable(
            new TableSchema('test-add-column', [
                new Column('id', ColumnType::STRING(), true),
                new Column('first_name', ColumnType::STRING()),
                new Column('last_name', ColumnType::STRING()),
                new Column('age', ColumnType::INTEGER())
            ])
        );

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
        $table = $this->database->createTable(
            new TableSchema('test-update-column', [
                new Column('id', ColumnType::STRING(), true),
                new Column('first_name', ColumnType::STRING()),
                new Column('last_name', ColumnType::STRING()),
                new Column('age', ColumnType::INTEGER())
            ])
        );

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
        $table = $this->database->createTable(
            new TableSchema('test-delete-column', [
                new Column('id', ColumnType::STRING(), true),
                new Column('first_name', ColumnType::STRING()),
                new Column('last_name', ColumnType::STRING()),
                new Column('age', ColumnType::INTEGER())
            ])
        );

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
}