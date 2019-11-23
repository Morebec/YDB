<?php 

namespace Morebec\YDB\Entity\QueryPlan;

use Morebec\YDB\YQL\ExpressionNode;
use PhpParser\Node\Stmt\Expression;

/**
 * Represents the need for a index scan
 */
class IndexScanStrategy extends QueryPlanStrategy
{
    /** @var array array of index names to be used by the scan strategy */
    private $indexNames;

    public function __construct(array $indexNames)
    {
        $this->indexNames = $indexNames;
    }

    /**
     * @return array
     */
    public function getIndexNames(): array
    {
        return $this->indexNames;
    }

    public function __toString()
    {
        return 'index_scan_strategy';
    }
}
