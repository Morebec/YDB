<?php

namespace Morebec\YDB\Service;

use Morebec\YDB\Contract\QueryInterface;
use Morebec\YDB\Contract\TableSchemaInterface;
use Morebec\YDB\Entity\QueryPlan\IdScanStrategy;
use Morebec\YDB\Entity\QueryPlan\IndexScanStrategy;
use Morebec\YDB\Entity\QueryPlan\QueryPlan;
use Morebec\YDB\Entity\QueryPlan\QueryPlanStrategy;
use Morebec\YDB\Entity\QueryPlan\QueryPlanStrategyComparator;
use Morebec\YDB\Entity\QueryPlan\TableScanStrategy;
use Morebec\YDB\Entity\Query\Query;
use Morebec\YDB\Exception\QueryException;
use Morebec\YDB\YQL\ExpressionNode;
use Morebec\YDB\YQL\TermNode;

/**
 * QueryPlanner is used to determine the best
 * startegy to perform a query based on the table's
 * structure and data.
 * Some of the strategies can be
 *     - table scan
 *     - index scan
 *     - filename scan
 *
 * By design it is only possible to query a single table at a time
 * Therefore we don't need a complex Query Plan tree.
 * We can simply compare the cost of different
 * QueryPlanStrategy and chose the best one.
 *
 * We leave the logic off the developers to use
 * the most efficient queries in order to perform
 * their own join.
 */
class QueryPlanner
{
    public function __construct()
    {
    }

    /**
     * Returns the strategy to use for a query
     * @param  Query  $query query
     * @return QueryPlanStrategy
     */
    public function createPlanForQuery(
        TableSchemaInterface $schema,
        Query $query
    ): QueryPlan {
        // We need to traverse the expression tree and determine for every
        // expression node the strategy to use
        $expr = $query->getExpressionNode();
        $strategy = $this->getStrategiesForExpression($schema, $expr);

        // Make sure we have at least one strategy
        if (!$strategy) {
            throw new QueryException(
                "Internal Error: Could not compute a query Plan for the given query, no stratgies computed"
            );
        }


        return new QueryPlan($strategy);
    }


    /**
     * Returns the strategies to uses for an expression's terms
     * @param  ExpressionNode $node       expression
     * @param  array
     *  $strategies built array of strategies
     */
    public function getStrategiesForExpression(
        TableSchemaInterface $schema,
        ExpressionNode $node
    ): QueryPlanStrategy {
        if ($node instanceof TermNode) {
            return $this->determineStrategyForTerm($schema, $node);
        }

        $leftNode = $node->getLeft();
        $leftValue = $this->getStrategiesForExpression($schema, $leftNode);

        $operator = $node->getOperator();
        if (!$operator) {
            return $leftValue;
        }

        $rightNode = $node->getRight();
        $rightValue = $this->getStrategiesForExpression($schema, $rightNode);

        return QueryPlanStrategyComparator::compare($leftValue, $operator, $rightValue);
    }

    /**
     * Determines the best strategy to use for a given term
     * @param  TermNode $node term node
     * @return QueryPlanStrategy
     */
    private function determineStrategyForTerm(
        TableSchemaInterface $schema,
        TermNode $node
    ): QueryPlanStrategy {
        $fieldName = $node->getTermFieldName();

        if ($fieldName === 'id') {
            return new IdScanStrategy([$node->getTermValue()]);
        }

        // If the column is indexed we will use an index scan
        if ($schema->getColumnByName($fieldName)->isIndexed()) {
            return new IndexScanStrategy([$node->getTermValue()]);
        }

        return new TableScanStrategy();
    }
}
