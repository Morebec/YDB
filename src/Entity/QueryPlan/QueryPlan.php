<?php 

namespace Morebec\YDB\Entity\QueryPlan;

use Morebec\YDB\Entity\QueryPlan\QueryPlanStrategy;

/**
 * Represents a query plan
 */
class QueryPlan
{
    /** @var QueryPlanStrategy */
    private $strategy;

    function __construct(QueryPlanStrategy $strategy)
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
        return sprintf("Query plan(%s)", $this->strategy);
    }
}
