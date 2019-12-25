<?php

namespace Morebec\YDB\Domain\YQL;

use Morebec\YDB\Domain\YQL\Query\Term;

/**
 * TermNode
 */
class TermNode extends ExpressionNode
{
    private $term;
    
    public function __construct(Term $c)
    {
        $this->term = $c;
        parent::__construct();
    }

    public function __toString()
    {
        return (string)$this->term;
    }

    public function getTerm(): Term
    {
        return $this->term;
    }

    public function getTermFieldName(): string
    {
        return $this->term->getFieldName();
    }

    public function getTermValue()
    {
        return $this->term->getValue();
    }
}
