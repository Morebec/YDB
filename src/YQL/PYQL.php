<?php 

namespace Morebec\YDB\YQL;

use Morebec\YDB\Contract\RecordInterface;
use Morebec\YDB\YQL\CriterionNode;
use Morebec\YDB\YQL\TreeNode;

/**
 * PHP YDB Query Language
 */
class PYQL
{
    /**
     * Evaluates a TreeNode to see if it matches a record
     * @param  TreeNode        $node   node
     * @param  RecordInterface $record record
     * @return bool                  true if it matches, otherwise false
     */
    public static function evaluateForRecord(
        TreeNode $node, 
        RecordInterface $record
    ): bool {
        
        if($node instanceof CriterionNode) {
            return $node->getCriterion()->matchesRecord($record);
        }

        $leftNode = $node->getLeft();
        $leftValue = $node->getLeft() ? 
                        self::evaluateForRecord($leftNode, $record) : false;

        $operator = $node->getOperator();
        if(!$operator) {
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
     * @param  TreeOperator $operator operator
     * @param  bool     $leftValue    left value
     * @return bool                   evaluated value
     */
    private static function evaluateOperator(
        bool $right, 
        TreeOperator $operator, 
        bool $left
    ): bool
    {
        switch ($operator) {
            case TreeOperator::AND:
                return $right && $left;
                break;

            case TreeOperator::OR:
                return $right || $left;
                break;
            
            default:
                throw new \Exception("Invalid operator '$operator'");
                break;
        }
    }
}