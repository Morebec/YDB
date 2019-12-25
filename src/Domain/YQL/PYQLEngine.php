<?php

namespace Morebec\YDB\Domain\YQL;

use Morebec\YDB\Domain\Model\Entity\Record;
use Morebec\YDB\Domain\YQL\Query\Query;

/**
 * PHP implementation of the YDB Query Language
 */
class PYQLEngine
{
    /**
     * Evaluates a Query to see if it matches a record
     * @param Query $query query
     * @param Record $record record
     * @return bool            true if it matches, otherwise false
     */
    public static function evaluateQueryForRecord(
        Query $query,
        Record $record
    ): bool {
        return self::evaluateExpressionForRecord($query->getExpressionNode(), $record);
    }

    /**
     * Evaluates a ExpressionNode to see if it matches a record
     * @param ExpressionNode $node node
     * @param Record $record record
     * @return bool            true if it matches, otherwise false
     */
    public static function evaluateExpressionForRecord(
        ExpressionNode $node,
        Record $record
    ): bool {
        if ($node instanceof TermNode) {
            return $node->getTerm()->matchesRecord($record);
        }

        $leftNode = $node->getLeft();
        $leftValue = self::evaluateExpressionForRecord($leftNode, $record);

        $operator = $node->getOperator();
        if (!$operator) {
            return $leftValue;
        }

        $rightNode = $node->getRight();
        $rightValue = self::evaluateExpressionForRecord($rightNode, $record);

        return self::evaluateOperator($leftValue, $operator, $rightValue);
    }

    /**
     * Evaluates a right and a left value with a logical operator
     * @param bool $right
     * @param ExpressionOperator $operator operator
     * @param bool $left
     * @return bool evaluated value
     */
    private static function evaluateOperator(
        bool $right,
        ExpressionOperator $operator,
        bool $left
    ): bool {
        if($right === $left) {
            return $right;
        }

        if ($operator->isEqualTo(ExpressionOperator::AND())) {
            return $right && $left;
        }

        if ($operator->isEqualTo(ExpressionOperator::OR())) {
            return $right || $left;
        }

        throw new \InvalidArgumentException("Invalid operator '$operator'");
    }
}
