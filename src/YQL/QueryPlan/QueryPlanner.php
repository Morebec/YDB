<?php


namespace Morebec\YDB\YQL\QueryPlan;

use Morebec\YDB\Document;
use Morebec\YDB\DocumentCollectionInterface;
use Morebec\YDB\Exception\QueryException;
use Morebec\YDB\Exception\QueryStrategyComputationFailedException;
use Morebec\YDB\YQL\ExpressionNode;
use Morebec\YDB\YQL\Parser\TautologyNode;
use Morebec\YDB\YQL\Query;
use Morebec\YDB\YQL\TermNode;

/**
 * Class QueryPlanner
 * The Query Planner is used to determine the best strategy to perform a query on a collection's data.
 * The available strategies are:
 * - Collection Scan: Scan every document in a collection
 * - Index Scan: Scan documents using a limited set of indexed data
 * - Id Scan: Similar to an index scan it scans the documents using their internal id (_id)
 * - Multi: Is a mixture of multiple strategies
 */
class QueryPlanner
{
    public function __construct()
    {
    }

    /**
     * Analyses a Query and returns a QueryPlan
     * @param Query $query
     * @param DocumentCollectionInterface $collection
     * @return QueryPlan
     * @throws QueryStrategyComputationFailedException
     */
    public function createPlanForQuery(Query $query, DocumentCollectionInterface $collection): QueryPlan
    {
        // We need to traverse the expression tree and determine for every expression node the strategy to use
        // Once that done, we try to optimize the strategies to use the fastest one.
        $expr = $query->getExpression();
        $strategy = $this->getStrategiesForExpressionNode($expr, $collection);

        if (!$strategy) {
           throw new QueryStrategyComputationFailedException('Could not compute a query Plan for the given query, no strategies computed');
        }

        return new QueryPlan($strategy);
    }

    /**
     * Determines the strategy to use for a an expression node
     * @param ExpressionNode $node
     * @param DocumentCollectionInterface $collection
     * @return QueryPlanStrategy
     */
    private function getStrategiesForExpressionNode(
        ExpressionNode $node,
        DocumentCollectionInterface $collection
    ): QueryPlanStrategy
    {
        if ($node instanceof TautologyNode) {
            return new CollectionScanStrategy();
        }

        if ($node instanceof TermNode) {
           return $this->determineStrategyForTerm($node, $collection);
        }

        /** @var ExpressionNode $leftNode */
        $leftNode = $node->getLeft();
        $leftValue = $this->getStrategiesForExpressionNode($leftNode, $collection);

        $operator = $node->getOperator();
        if(!$operator) {
            return $leftValue;
        }

        /** @var ExpressionNode $rightNode */
        $rightNode = $node->getRight();
        $rightValue = $this->getStrategiesForExpressionNode($rightNode, $collection);

        return QueryPlanStrategyComparator::compare($leftValue, $operator, $rightValue);
    }

    /**
     * Determiens the best strategy to use for a given Term node
     * @param TermNode $node
     * @param DocumentCollectionInterface $collection
     * @return QueryPlanStrategy
     */
    private function determineStrategyForTerm(
        TermNode $node,
        DocumentCollectionInterface $collection
    ): QueryPlanStrategy
    {
        $fieldName = $node->getTermField();

        // If we are dealing with the internal _id
        if ($fieldName === Document::ID_FIELD) {
            return new IdScanStrategy([$node->getTermValue()]);
        }

        if ($collection->hasIndexOnField($fieldName)) {
            return new IndexScanStrategy([$node->getTermValue()]);
        }

        // No more options, CollectionScan
        return new CollectionScanStrategy();
    }
}