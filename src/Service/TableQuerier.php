<?php

namespace Morebec\YDB\Service;

use Morebec\YDB\Contract\QueryInterface;
use Morebec\YDB\Contract\RecordInterface;
use Morebec\YDB\Contract\TableSchemaInterface;
use Morebec\YDB\Entity\QueryResultInterface;
use Morebec\YDB\Enum\QuerySource;
use Morebec\YDB\Exception\TableNotFoundException;
use Morebec\YDB\YQL\TermNode;
use Morebec\YDB\YQL\ExpressionNode;

/**
 * Class responsible for querying tables
 */
class TableQuerier
{
    /** @var TableManager */
    private $tableManager;

    public function __construct(TableManager $tableManager)
    {
        $this->tableManager = $tableManager;
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
        // First validate that the table in the query exists
        if (!$this->tableManager->tableExists($tableName)) {
            throw new QueryException("Invalid query: table '$tableName' does not exist");
        }

        // We now need to find the records that are potential candidates
        // for the query.
        // The best case scenario, we'll be able to get them through indexes.
        // else, we'll need to check with every record in the table.
        
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

        // Now we need to traverse the expression tree
        // And evaluate every expression with the appropriate
        // record candidates
    }

    private function getColumnsForQuery(Query $query): array
    {
        $expr = $query->getExpression();

        $columns = [];
        $this->getColumnsForExpression($expr, $columns);

        // Make values unique in an efficient
        return array_flip(array_values($columns));
    }

    private function getColumnsForExpression(ExpressionNode $node, array $columns)
    {
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
