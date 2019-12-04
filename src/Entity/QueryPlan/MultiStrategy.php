<?php

namespace Morebec\YDB\Entity\QueryPlan;

use Assert\Assertion;

/**
 * Represents a strategy that relies on multiple strategies
 */
class MultiStrategy extends QueryPlanStrategy
{
    /** @var array array of QueryPlanStrategy objects */
    private $strategies;

    public function __construct(array $strategies)
    {
        Assertion::count($strategies, 2, "A multistrategy must have at least 2");
        $this->strategies = $strategies;
    }

    /**
     * Indicates if a strategy of a certain class type exists in the plan
     * @param  string  $className name of the class
     * @return boolean            true if strategy is in plan, otherwise false
     */
    public function hasStrategyType(string $className): bool
    {
        foreach ($this->strategies as $strategy) {
            if (get_class($strategy) === $className) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getStrategies(): array
    {
        return $this->strategies;
    }

    public function __toString()
    {
        $s = join(',', $this->strategies);
        return 'multi_scan_strategy($s)';
    }
}