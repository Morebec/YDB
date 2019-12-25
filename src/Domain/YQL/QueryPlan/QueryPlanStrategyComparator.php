<?php

namespace Morebec\YDB\Domain\YQL\QueryPlan;

use Morebec\YDB\Domain\YQL\ExpressionOperator;

/**
 * Compares two strategies, and determine
 * which one should be used
 */
class QueryPlanStrategyComparator
{
    /**
     * Compares two strategy and returns the best one,
     * or both if they are equally needed
     * @param  QueryPlanStrategy      $leftOperand
     * @param  ExpressionOperator $operator
     * @param  QueryPlanStrategy      $rightOperand
     * @return QueryPlanStrategy
     */
    public static function compare(
        QueryPlanStrategy $leftOperand,
        ExpressionOperator $operator,
        QueryPlanStrategy $rightOperand
    ): QueryPlanStrategy {
        if ($operator->isEqualTo(ExpressionOperator::OR())) {
            return self::compareOrOperator($leftOperand, $rightOperand);
        }

        if ($operator->isEqualTo(ExpressionOperator::AND())) {
            return self::compareAndOperator($leftOperand, $rightOperand);
        }

        throw new \InvalidArgumentException("Unexpected Operand '{$operator}'");
    }

    /**
     * Compares strategy in the context of an OR
     * @param  QueryPlanStrategy $leftOperand  left operand
     * @param  QueryPlanStrategy $rightOperand right operand
     * @return QUeryPlanStrategy
     */
    private static function compareOrOperator(
        QueryPlanStrategy $leftOperand,
        QueryPlanStrategy $rightOperand
    ): QueryPlanStrategy {
        // If any of the two are TableScans, return the TableScans
        if (self::isOfType($leftOperand, TableScanStrategy::class) ||
            self::isOfType($rightOperand, TableScanStrategy::class)) {
            $leftIsIdScan = self::isOfType($leftOperand, TableScanStrategy::class);
            return  $leftIsIdScan ? $leftOperand : $rightOperand;
        }

        // If both are the same, return a merged version of them
        if (self::isOfType($leftOperand, get_class($rightOperand))) {
            return self::mergeStrategies($leftOperand, $rightOperand);
        }

        // Else we will need to deal with a multiple strategy
        // However, we must handle the case where
        // one of them is a multi strategy already
        // or both of them are multi strategies
        // if both of them are multi strategy it was already dealt with
        // by comparing two that the two strategies are equal.
        
        // If any of the two are MultiStrategy, we must append the new strategy
        // to the multi strategy
        if (self::isOfType($leftOperand, MultiStrategy::class) ||
            self::isOfType($rightOperand, MultiStrategy::class)) {
            return self::mergeStrategies($leftOperand, $rightOperand);
        }

        // We logically have an IndexScan and an IdScan
        // merge the two together
        return new MultiStrategy([$leftOperand, $rightOperand]);
    }

    /**
     * Compares strategy in the context of an AND
     * @param  QueryPlanStrategy $leftOperand  left operand
     * @param  QueryPlanStrategy $rightOperand right operand
     * @return QUeryPlanStrategy
     */
    private static function compareAndOperator(
        QueryPlanStrategy $leftOperand,
        QueryPlanStrategy $rightOperand
    ): QueryPlanStrategy {
        $leftOperandClass = get_class($leftOperand);
        $rightOperandClass = get_class($rightOperand);

        // If both are the same, return a merged version of them
        if ($leftOperandClass === $rightOperandClass) {
            return self::mergeStrategies($leftOperand, $rightOperand);
        }

        // If any of the two are Id scans, return the Id scan
        if (self::isOfType($leftOperand, IdScanStrategy::class) ||
            self::isOfType($rightOperand, IdScanStrategy::class)) {
            $leftIsIdScan = self::isOfType($leftOperand, IdScanStrategy::class);
            return  $leftIsIdScan ? $leftOperand : $rightOperand;
        }

        // Else we are dealing with a TableScan and an IndexScan
        // Return the IndexScan
        $leftIsIndexScan = self::isOfType($leftOperand, IndexScanStrategy::class);
        return $leftIsIndexScan ? $leftOperand : $rightOperand;
    }

    /**
     * Merges two strategies together
     * @param QueryPlanStrategy $strategyA
     * @param QueryPlanStrategy $strategyB
     * @return QueryPlanStrategy
     */
    private static function mergeStrategies(
        QueryPlanStrategy $strategyA,
        QueryPlanStrategy $strategyB
    ): QueryPlanStrategy {
        // Validate input first
        // If any of the two are multi strategy
        // it can be merged
        // In any other case they need to be of the same type
        if (!self::isOfType($strategyA, MultiStrategy::class) &&
           !self::isOfType($strategyB, MultiStrategy::class)) {
            // None of the two are multi, make further validation
            $strategyAClass = get_class($strategyA);
            $strategyBClass = get_class($strategyB);
            if($strategyAClass !== $strategyBClass) {
                throw new \InvalidArgumentException(sprintf(
                    'Cannot merge two strategies of different types: found %s and %s',
                    $strategyAClass,
                    $strategyBClass
                ));
            }
        }

        // If both are multi we must merge both strategies"
        if (
            self::isOfType($strategyA, MultiStrategy::class) &&
            self::isOfType($strategyB, MultiStrategy::class)
          ) {
            /** @var MultiStrategy $strategyA */
            /** @var MultiStrategy $strategyB */
            return self::mergeMultiScans($strategyA, $strategyB);
        }

        // If any of the two are MultiStrategy, we must append the new strategy
        // to the multi strategy
        if (self::isOfType($strategyA, MultiStrategy::class) ||
            self::isOfType($strategyB, MultiStrategy::class)) {
            $strategyAIsMulti = self::isOfType($strategyA, MultiStrategy::class);
            $multi = $strategyAIsMulti ? $strategyA : $strategyB;
            $nonMulti = $strategyAIsMulti ? $strategyB : $strategyA;
            /** @var MultiStrategy $multi */
            return self::mergeMultiScanAndNonMulti($multi, $nonMulti);
        }

        if (self::isOfType($strategyA, IndexScanStrategy::class)) {
            /** @var IndexScanStrategy $strategyA */
            /** @var IndexScanStrategy $strategyB */
            return self::mergeIndexScans($strategyA, $strategyB);
        }

        if (self::isOfType($strategyA, IdScanStrategy::class)) {
            /** @var IdScanStrategy $strategyA */
            /** @var IdScanStrategy $strategyB */
            return self::mergeIdScans($strategyA, $strategyB);
        }

        // We probably have two TableStrategies
        // Since they are equal return any of them
        return $strategyA;
    }

    /**
     * Merges two IndexScans together
     * @param IndexScanStrategy $strategyA index scan A
     * @param IndexScanStrategy $strategyB index scan B
     * @return IndexScanStrategy
     */
    public static function mergeIndexScans(
        IndexScanStrategy $strategyA,
        IndexScanStrategy $strategyB
    ): IndexScanStrategy {
        // Merge indexes
        return new IndexScanStrategy(array_merge(
            $strategyA->getIndexNames(),
            $strategyB->getIndexNames()
        ));
    }

    /**
     * Merges two ids cans together
     * @param  IdScanStrategy $strategyA id scan
     * @param  IdScanStrategy $strategyB id scan
     * @return IdScanStrategy
     */
    public static function mergeIdScans(
        IdScanStrategy $strategyA,
        IdScanStrategy $strategyB
    ): IdScanStrategy {
        // Merge ids
        return new IdScanStrategy(array_merge(
            $strategyA->getIds(),
            $strategyB->getIds()
        ));
    }

    /**
     * Merges two multiscans
     * @param  MultiStrategy $strategyA multiscan
     * @param  MultiStrategy $strategyB multiscan
     * @return MultiStrategy
     */
    public static function mergeMultiScans(
        MultiStrategy $strategyA,
        MultiStrategy $strategyB
    ): MultiStrategy {
        // TODO Document this
        $strategies = array_merge($strategyA, $strategyB);

        // Now sort into type arrays
        $types = [];

        foreach ($strategies as $s) {
            $sClass = get_class($s);
            if (!array_key_exists($sClass, $types)) {
                $types[$sClass] = [];
            }
            $types[$sClass][] = $s;
        }

        // Merge by types
        $strategies = [];
        foreach ($types as $typeArr) {
            foreach ($typeArr as $a) {
                foreach ($typeArr as $b) {
                    if ($a === $b) {
                        continue;
                    }
                    if (!self::isOfType($a, $b)) {
                        continue;
                    }
                    $strategies[] = self::mergeStrategies($a, $b);
                }
                break; // We don't want to traverse again
            }
        }

        return new MultiStrategy($strategies);
    }

    /**
     * Merges a MultiScan with another strategy
     * @param  MultiStrategy     $multiScan multiscan
     * @param  QueryPlanStrategy $strategy  other strategy
     * @return MultiStrategy
     */
    public static function mergeMultiScanAndNonMulti(
        MultiStrategy $multiScan,
        QueryPlanStrategy $strategy
    ): MultiStrategy {
        $strategies = [];
        foreach ($multiScan->getStrategies() as $s) {
            if (self::isOfType($strategy, $s)) {
                $s = self::mergeStrategies($strategy, $s);
            }
            $strategies[] = $s;
        }

        return new MultiStrategy($strategies);
    }

    /**
     * Indicates if a QueryPlanStrategy is of a certain class type
     * @param  QueryPlanStrategy $strategy  strategy
     * @param  string            $typeClass class type
     * @return boolean                      true if same type, otherwise false
     */
    private static function isOfType(
        QueryPlanStrategy $strategy,
        string $typeClass
    ): bool {
        return get_class($strategy) === $typeClass;
    }
}
