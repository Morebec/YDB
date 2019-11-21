<?php 

namespace Morebec\YDB\YQL;

use Morebec\YDB\Entity\Query\Criterion;

/**
 * CriterionNode
 */
class CriterionNode extends TreeNode
{
    private $criterion;
    
    function __construct(Criterion $c)
    {
        $this->criterion = $c;
    }

    public function __toString()
    {
        return (string)$this->criterion;
    }

    public function getCriterion(): Criterion
    {
        return $this->criterion;
    }
}
