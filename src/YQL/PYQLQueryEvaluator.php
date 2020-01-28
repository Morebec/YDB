<?php

namespace Morebec\YDB\YQL;

use InvalidArgumentException;
use Morebec\YDB\Document;
use Morebec\YDB\YQL\Query\ExpressionQuery;

/**
 * PHP implementation of the YDB Query Language
 */
class PYQLQueryEvaluator
{
    /**
     * Evaluates a Query to see if it matches a record
     * @param ExpressionQuery $query query
     * @param Document $document record
     * @return bool            true if it matches, otherwise false
     */
    public static function evaluateQueryForDocument(
        ExpressionQuery $query,
        Document $document
    ): bool {
        return self::evaluateExpressionForDocument($query->getExpression(), $document);
    }

    /**
     * Evaluates a ExpressionNode to see if it matches a record
     * @param ExpressionNode $node node
     * @param Document $record record
     * @return bool            true if it matches, otherwise false
     */
    public static function evaluateExpressionForDocument(
        ExpressionNode $node,
        Document $record
    ): bool {
        if ($node instanceof TermNode) {
            return $node->matchesDocument($record);
        }

        /** @var ExpressionNode $leftNode */
        $leftNode = $node->getLeft();
        // NOTE: There should logically always a left node unless it is a term node
        $leftValue = self::evaluateExpressionForDocument($leftNode, $record);

        $operator = $node->getOperator();
        if (!$operator) {
            return $leftValue;
        }

        /** @var ExpressionNode $rightNode */
        $rightNode = $node->getRight();
        $rightValue = self::evaluateExpressionForDocument($rightNode, $record);

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

        throw new InvalidArgumentException("Invalid operator '$operator'");
    }
}
