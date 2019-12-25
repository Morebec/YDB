<?php

use Morebec\YDB\ColumnBuilder;
use Morebec\YDB\Domain\YQL\Query\Operator;
use Morebec\YDB\Domain\YQL\QueryPlan\IdScanStrategy;
use Morebec\YDB\Domain\YQL\QueryPlan\IndexScanStrategy;
use Morebec\YDB\Domain\YQL\QueryPlan\MultiStrategy;
use Morebec\YDB\Domain\YQL\QueryPlan\TableScanStrategy;
use Morebec\YDB\ExpressionBuilder;
use Morebec\YDB\legacy\Service\QueryPlanner;
use Morebec\YDB\TableSchemaBuilder;
use PHPUnit\Framework\TestCase;

/**
 * QueryPlannerTest
 */
class QueryPlannerTest extends TestCase
{
    public function testOrExpressionReturnsIndexScan(): void
    {
        $expr = ExpressionBuilder::where('indexed_field', Operator::EQUAL(), 'value_1')
                                 ->build()
        ;

        $schema = TableSchemaBuilder::withName('table')
                            ->withColumn(ColumnBuilder::withName('indexed_field')->withStringType()->indexed()->build())
                  ->build()
        ;
        $planner = new QueryPlanner();
        $strategy = $planner->getStrategiesForExpression($schema, $expr);

        $this->assertInstanceOf(IndexScanStrategy::class, $strategy);
    }    

    public function testOrExpressionReturnsTableScan(): void
    {
        $schema = TableSchemaBuilder::withName('test-table')
            ->withColumn(ColumnBuilder::withName('field')
                                        ->withStringType()
                                        ->build()
            )
            ->build()
        ;

        $expr = ExpressionBuilder::where('field', Operator::EQUAL(), 'value_1')
                                 ->build()
        ;

        $planner = new QueryPlanner();
        $strategy = $planner->getStrategiesForExpression($schema, $expr);

        $this->assertInstanceOf(TableScanStrategy::class, $strategy);
    }

    public function testOrExpressionReturnsIdScan(): void
    {
        $schema = TableSchemaBuilder::withName('test-table')
            ->withColumn(ColumnBuilder::withName('indexed_field')
                                        ->withStringType()
                                        ->Indexed()
                                        ->build()
            )
            ->build()
        ;

        $expr = ExpressionBuilder::where('id', Operator::EQUAL(), 'value_1')
                                 ->build()
        ;

        $planner = new QueryPlanner();
        $strategy = $planner->getStrategiesForExpression($schema, $expr);

        $this->assertInstanceOf(IdScanStrategy::class, $strategy);
    }

    public function testOrExpressionReturnsMultiStrategy(): void
    {
        $schema = TableSchemaBuilder::withName('test-table')
            ->withColumn(ColumnBuilder::withName('indexed_field')
                                        ->withStringType()
                                        ->Indexed()
                                        ->build()
            )
            ->build()
        ;
        $expr = ExpressionBuilder::where('id', Operator::EQUAL(), 'value_1')
                                 ->orWhere('indexed_field', Operator::EQUAL(), 'value_1')
                                 ->build()
        ;

        $planner = new QueryPlanner();
        /** @var MultiStrategy $strategy */
        $strategy = $planner->getStrategiesForExpression($schema, $expr);

        $this->assertInstanceOf(MultiStrategy::class, $strategy);
        $this->assertTrue($strategy->hasStrategyType(IndexScanStrategy::class));
        $this->assertTrue($strategy->hasStrategyType(IdScanStrategy::class));
    }
}