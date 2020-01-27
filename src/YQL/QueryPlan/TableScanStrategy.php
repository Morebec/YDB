<?php

namespace Morebec\YDB\YQL\QueryPlan;

/**
 * TableScanStrategy
 */
class TableScanStrategy extends QueryPlanStrategy
{
    public function __toString()
    {
        return 'table_scan_strategy';
    }
}
