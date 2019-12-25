<?php

namespace Morebec\YDB\legacy\Service;

use Morebec\ValueObjects\DateTime\Time\Timestamp;
use Morebec\YDB\Contract\QueryInterface;
use Morebec\YDB\Contract\QueryResultInterface;
use Morebec\YDB\Contract\Record;
use Morebec\YDB\Contract\TableSchemaInterface;
use Morebec\YDB\Entity\QueryPlan\QueryPlan;
use Morebec\YDB\Entity\Query\Query;
use Morebec\YDB\Entity\Query\QueryResult;
use Morebec\YDB\Entity\Query\QueryStatistics;
use Morebec\YDB\Entity\Query\TermPlanNode;
use Morebec\YDB\Enum\QuerySource;
use Morebec\YDB\Exception\QueryException;
use Morebec\YDB\Exception\TableNotFoundException;
use Morebec\YDB\Service\TableManager;
use Morebec\YDB\YQL\ExpressionNode;
use Morebec\YDB\YQL\TermNode;
use Psr\Log\LogLevel;

/**
 * Class responsible for querying tables
 */
class TableQuerier
{
    /** @var TableManager */
    private $tableManager;

    /** @var QueryPlanner */
    private $queryPlanner;

    /** @var QueryPlanExecutor */
    private $queryPlanExecutor;

    public function __construct(TableManager $tableManager)
    {
        $this->tableManager = $tableManager;
        $this->queryPlanner = new QueryPlanner();
        $this->queryPlanExecutor = new QueryPlanExecutor($tableManager);
    }

    /**
     * Queries a table and returns the QueryRecord
     * that matched the query
     * @param  string         $tableName name of the table to query
     * @param  QueryInterface $query     query
     * @return \Generator
     */
    public function queryTable(
        string $tableName,
        QueryInterface $query
    ): QueryResultInterface {
        $this->tableManager->log(LogLevel::INFO, "Querying table '$tableName'", [
            'table_name' => $tableName,
            'query' => (string)$query
        ]);

        $startTime = Timestamp::now();

        // VALIDATE THAT THE TABLE IN THE QUERY EXISTS
        if (!$this->tableManager->tableExists($tableName)) {
            throw new QueryException("Invalid query: table '$tableName' does not exist");
        }

        // VALIDATE THE COLUMNS IN USE IN THE QUERY
        // Run through the Query's expression and find the columns that are in use
        $columns = $this->getColumnsForQuery($query);

        // Check if these columns are valid in the table's schema
        $schema = $this->tableManager->getTableSchema($tableName);
        foreach ($columns as $col) {
            if (!$schema->columnWithNameExists($col)) {
                throw new QueryException(
                    "Invalid query: table '$tableName' does not have a column named '$col'"
                );
            }
        }

        // CREATE A QUERY PLAN
        $this->tableManager->log(LogLevel::INFO, "Creating query plan for '$tableName'", [
            'table_name' => $tableName,
            'query' => (string)$query,
        ]);
        $queryPlanStartTime = Timestamp::now();

        $plan = $this->queryPlanner->createPlanForQuery($schema, $query);
        $queryPlanEndTime = Timestamp::now();


        $this->tableManager->log(LogLevel::INFO, "Created query plan for table '$tableName'", [
            'table_name' => $tableName,
            'query' => (string)$query,
            'query_plan' => (string)$plan

        ]);

        // Execute the plan
        $matchIterator = $this->queryPlanExecutor->execute($tableName, $query, $plan);
        $endTime = Timestamp::now();

        $stats = new QueryStatistics(
            $startTime, 
            $endTime,
            $queryPlanStartTime,
            $queryPlanEndTime
        );

        $this->tableManager->log(LogLevel::INFO, "Executed query plan for table '$tableName'", [
            'table_name' => $tableName,
            'query' => (string)$query,
            'query_plan' => (string)$plan,
            'duration' => $stats->getDuration(),
            'query_planner_duration' => $stats->getQueryPlannerDuration()
        ]);

        return new QueryResult($matchIterator, $query, $stats);
    }


    /**
     * Returns the columns in use in the query
     * @param  Query  $query query
     * @return array
     */
    private function getColumnsForQuery(Query $query): array
    {
        $expr = $query->getExpressionNode();

        $columns = [];
        $this->getColumnsForExpression($expr, $columns);

        // Make values unique in an efficient
        return array_keys(array_flip($columns));
    }

    /**
     * Returns the columns in use in an expression node
     * @param  ExpressionNode $node    node
     * @param  array          $columns columns in use (will be populated)
     */
    private function getColumnsForExpression(
        ExpressionNode $node,
        array &$columns
    ): void {
        if ($node instanceof TermNode) {
            $columns[] = $node->getTerm()->getFieldName();
            return;
        }

        $leftNode = $node->getLeft();
        $this->getColumnsForExpression($leftNode, $columns);

        $rightNode = $node->getRight();
        if ($rightNode) {
            $this->getColumnsForExpression($rightNode, $columns);
        }
    }
}
