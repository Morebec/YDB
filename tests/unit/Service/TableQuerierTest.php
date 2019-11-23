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
}