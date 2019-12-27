<?php

namespace Morebec\YDB;

use Morebec\YDB\Domain\YQL\ExpressionNode;
use Morebec\YDB\Domain\YQL\Query\Operator;
use Morebec\YDB\Domain\YQL\Query\Query;
use Morebec\YDB\Domain\YQL\Query\Term;

/**
 * QueryBuilder
 */
class QueryBuilder extends ExpressionBuilder
{
    /**
     * Creates a find all clause to the query
     * @return QueryBuilder
     */
    public function findAll(): QueryBuilder
    {
        //return new static(new TermNode());
    }

    /**
     * Adds a the first clause of the query
     * @param  string   $fieldName fieldName
     * @param  Operator $operator  Operator
     * @param  mixed   $value     value
     * @return QueryBuilder
     */
    public function find(string $fieldName, Operator $operator, $value): QueryBuilder
    {
        $exp = ExpressionBuilder::where($fieldName, $operator, $value)
                           ->build();

        $qb = new static(new ExpressionNode());
        $qb->addAnd(new Term($fieldName, $operator, $value));
        return $qb;
    }

    /**
     * Builds the Query and returns it
     * @return Query
     */
    public function build(): ExpressionNode
    {
        return new Query($this->root);
    }
}
