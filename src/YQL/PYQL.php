<?php

namespace Morebec\YDB\YQL;

use Morebec\YDB\Contract\RecordInterface;
use Morebec\YDB\YQL\TermNode;
use Morebec\YDB\YQL\ExpressionNode;

/**
 * PHP implementation of the YDB Query Language
 */
class PYQL
{
    
    
    /**
     * Evaluates a ExpressionNode to see if it matches a record
     * @param  ExpressionNode        $node   node
     * @param  RecordInterface $record record
     * @return bool                  true if it matches, otherwise false
     */
    public static function evaluateForRecord(
        ExpressionNode $node,
        RecordInterface $record
    ): bool {
        if ($node instanceof TermNode) {
            return $node->getTerm()->matchesRecord($record);
        }

        $leftNode = $node->getLeft();
        $leftValue = $node->getLeft() ?
                        self::evaluateForRecord($leftNode, $record) : false;

        $operator = $node->getOperator();
        if (!$operator) {
            return $leftValue;
        }

        $rightNode = $node->getRight();
        $rightValue = $node->getRight() ?
                        self::evaluateForRecord($rightNode, $record) : false;

        return self::evaluateOperator($leftValue, $operator, $rightValue);
    }

    /**
     * Evaluates a right and a left value with an operator
     * @param  bool     $rightValue   right value
     * @param  ExpressionOperator $operator operator
     * @param  bool     $leftValue    left value
     * @return bool                   evaluated value
     */
    private static function evaluateOperator(
        bool $right,
        ExpressionOperator $operator,
        bool $left
    ): bool {
        switch ($operator) {
            case ExpressionOperator::AND:
                return $right && $left;
                break;

            case ExpressionOperator::OR:
                return $right || $left;
                break;
            
            default:
                throw new \Exception("Invalid operator '$operator'");
                break;
        }
    }
}
