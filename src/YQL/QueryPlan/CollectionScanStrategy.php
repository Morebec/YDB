<?php

namespace Morebec\YDB\YQL\QueryPlan;

/**
 * CollectionScanStrategy
 */
class CollectionScanStrategy extends QueryPlanStrategy
{
    public function __toString()
    {
        return 'table_scan_strategy';
    }
}
