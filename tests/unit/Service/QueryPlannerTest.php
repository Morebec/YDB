<?php 

use Morebec\YDB\ColumnBuilder;
use Morebec\YDB\DatabaseConfig;
use Morebec\YDB\DatabaseConnection;
use Morebec\YDB\Entity\QueryPlan\IdScanStrategy;
use Morebec\YDB\Entity\QueryPlan\IndexScanStrategy;
use Morebec\YDB\Entity\QueryPlan\MultiStrategy;
use Morebec\YDB\Entity\QueryPlan\TableScanStrategy;
use Morebec\YDB\Entity\Query\Operator;
use Morebec\YDB\ExpressionBuilder;
use Morebec\YDB\Service\Database;
use Morebec\YDB\Service\QueryPlanner;
use Morebec\YDB\TableSchemaBuilder;

/**
 * QueryPlannerTest
 */
class QueryPlannerTest extends \Codeception\Test\Unit
{
    private function createDatabase(string $databaseName): DatabaseConnection
    {
        $dbPath = codecept_output_dir() . 'data/' . $databaseName;
        $config = new DatabaseConfig($dbPath);
        $conn = Database::getConnection($config);

        $conn->createDatabase();

        return $conn;
    }

    public function testOrExpressionReturnsIndexScan()
    {
        $conn = $this->createDatabase('test-or-expression-returns-index-scan');

        $schema = TableSchemaBuilder::withName('test-table')
            ->withColumn(ColumnBuilder::withName('indexed_field')
                                        ->withStringType()
                                        ->Indexed()
                                        ->build()
            )
            ->build()
        ;
        $conn->createTable($schema);

        $expr = ExpressionBuilder::where('indexed_field', Operator::EQUAL(), 'value_1')
                                 ->build()
        ;

        $planner = new QueryPlanner();
        $strategy = $planner->getStrategiesForExpression($schema, $expr);

        $this->assertInstanceOf(IndexScanStrategy::class, $strategy);

        // Cleanup
        $conn->deleteDatabase();
    }    

    public function testOrExpressionReturnsTableScan()
    {
        
        $conn = $this->createDatabase('test-or-expression-returns-table-scan');

        $schema = TableSchemaBuilder::withName('test-table')
            ->withColumn(ColumnBuilder::withName('field')
                                        ->withStringType()
                                        ->build()
            )
            ->build()
        ;
        $conn->createTable($schema);

        $expr = ExpressionBuilder::where('field', Operator::EQUAL(), 'value_1')
                                 ->build()
        ;

        $planner = new QueryPlanner();
        $strategy = $planner->getStrategiesForExpression($schema, $expr);

        $this->assertInstanceOf(TableScanStrategy::class, $strategy);

        $conn->deleteDatabase();
    }

    public function testOrExpressionReturnsIdScan()
    {
        
        $conn = $this->createDatabase('test-or-expression-returns-id-scan');

        $schema = TableSchemaBuilder::withName('test-table')
            ->withColumn(ColumnBuilder::withName('indexed_field')
                                        ->withStringType()
                                        ->Indexed()
                                        ->build()
            )
            ->build()
        ;
        $conn->createTable($schema);

        $expr = ExpressionBuilder::where('id', Operator::EQUAL(), 'value_1')
                                 ->build()
        ;

        $planner = new QueryPlanner();
        $strategy = $planner->getStrategiesForExpression($schema, $expr);

        $this->assertInstanceOf(IdScanStrategy::class, $strategy);

        // Cleanup
        $conn->deleteDatabase();
    }

    public function testOrExpressionReturnsMultiStrategy()
    {
        
        
        $conn = $this->createDatabase('test-or-expression-returns-id-scan');

        $schema = TableSchemaBuilder::withName('test-table')
            ->withColumn(ColumnBuilder::withName('indexed_field')
                                        ->withStringType()
                                        ->Indexed()
                                        ->build()
            )
            ->build()
        ;
        $conn->createTable($schema);

        $expr = ExpressionBuilder::where('id', Operator::EQUAL(), 'value_1')
                                 ->orWhere('indexed_field', Operator::EQUAL(), 'value_1')
                                 ->build()
        ;

        $planner = new QueryPlanner();
        $strategy = $planner->getStrategiesForExpression($schema, $expr);

        $this->assertInstanceOf(MultiStrategy::class, $strategy);

        // Cleanup
        $conn->deleteDatabase();
        
    }
}