<?php

namespace Morebec\YDB\YQL;

use Morebec\YDB\YQL\Query\Term;
use Morebec\YDB\YQL\Query\TermOperator;

/**
 * TermNode
 */
class TermNode extends ExpressionNode
{
    private $term;
    
    public function __construct(string $field, TermOperator $operator, $value)
    {
        $this->term = new Term($field, $operator, $value);
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
        return $this->term->getField();
    }

    public function getTermValue()
    {
        return $this->term->getValue();
    }
}
