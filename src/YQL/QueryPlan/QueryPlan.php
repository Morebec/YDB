<?php

namespace Morebec\YDB\YQL\QueryPlan;

/**
 * Represents a query plan
 */
class QueryPlan
{
    /** @var QueryPlanStrategy */
    private $strategy;

    public function __construct(QueryPlanStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @return QueryPlanStrategy
     */
    public function getStrategy(): QueryPlanStrategy
    {
        return $this->strategy;
    }

    public function __toString()
    {
        return "Query plan({$this->strategy})";
    }
}
