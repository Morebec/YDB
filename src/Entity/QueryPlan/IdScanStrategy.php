<?php 

namespace Morebec\YDB\Entity\QueryPlan;

/**
 * Represents the need for loading records by their id (filename)
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
