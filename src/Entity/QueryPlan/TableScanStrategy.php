<?php 

namespace Morebec\YDB\Entity\QueryPlan;

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
