<?php  

use Morebec\ValueObjects\File\Directory;
use Morebec\YDB\Column;
use Morebec\YDB\ColumnType;
use Morebec\YDB\Database;
use Morebec\YDB\Operator;
use Morebec\YDB\QueryBuilder;
use Morebec\YDB\Record;
use Morebec\YDB\RecordId;
use Morebec\YDB\TableSchema;

/**
 * Sandbox
 */
class SandboxTest extends \Codeception\Test\Unit
{
    private $database;

    public function _before()
    {
        $this->database = new Database(
            Directory::fromStringPath(__DIR__ . '/../_data/sandbox-db')
        );
    }

    public function testIndexation()
    {

        if(!$table = $this->database->getTableByName('test-index')) {        
            $table = $this->database->createTable(
                new TableSchema('test-index', [
                    new Column('id', ColumnType::STRING(), true),
                    new Column('first_name', ColumnType::STRING()),
                    new Column('last_name', ColumnType::STRING()),
                    new Column('age', ColumnType::INTEGER(), true)
                ])
            );
        }

        for ($i=0; $i < 200; $i++) { 
            $record = new Record(
                RecordId::generate(),
                [
                    'first_name' => 'James',
                    'last_name' => 'Bond',
                    'age' => $i % 4
                ]
            );
            $table->addRecord($record);
        }


        $r = $table->query(
            QueryBuilder::find('age', Operator::EQUAL(), 3)->build()
        );

        $this->assertTrue(count($r) != 0);
    }    
}