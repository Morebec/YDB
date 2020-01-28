<?php

namespace Morebec\YDB\YQL\QueryPlan;

/**
 * Represents the need for loading documents by their _id
 */
class IdScanStrategy extends QueryPlanStrategy
{
    /** @var array array of index names to be used by the scan strategy */
    private $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    /**
     * @return array
     */
    public function getIds(): array
    {
        return $this->ids;
    }

    public function __toString()
    {
        return 'id_scan_strategy';
    }
}
