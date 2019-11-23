<?php

namespace Morebec\YDB\YQL;

use Morebec\YDB\Entity\Query\Term;

/**
 * TermNode
 */
class TermNode extends ExpressionNode
{
    private $term;
    
    public function __construct(Term $c)
    {
        $this->term = $c;
    }

    public function __toString()
    {
        return (string)$this->term;
    }

    public function getTerm(): Term
    {
        return $this->term;
    }
}
