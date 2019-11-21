<?php 

namespace Morebec\YDB;

use Morebec\YDB\Contract\QueryInterface;
use Morebec\YDB\Entity\Query\Operator;
use Morebec\YDB\Entity\Query\Query;
use Morebec\YDB\Entity\Query\TautologyCriterion;
use Morebec\YDB\YQL\CriterionNode;
use Morebec\YDB\YQL\TreeNode;

/**
 * QueryBuilder
 */
class QueryBuilder extends ExpressionBuilder
{   

    /**
     * Creates a find all clause to the query
     * @return QUeryBuilder
     */
    public function findAll(): QueryBuilder
    {
        return new static(new CriterionNode(new TautologyCriterion()));
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

        $qb = new static(new CriterionBuilder);
        $qb->addAnd(new Criterion($fieldName, $operator, $value));
        return $qb;
    }

    /**
     * Builds the Query
     * @return Query
     */
    public function build(): QueryInterface
    {   
        return new Query($this->root);
    }
}