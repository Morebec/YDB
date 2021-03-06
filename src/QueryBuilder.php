<?php 

namespace Morebec\YDB;

use Morebec\YDB\Database\QueryInterface;

/**
 * QueryBuilder is a helper class to easily build queries
 */
class QueryBuilder
{
    /** @var array ors */
    private $ors;

    /** @var array ands */
    private $ands;

    function __construct()
    {
        $this->ors = [];
        $this->ands = [];
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
        $qb = new static();
        $qb->addAnd(new Criterion($fieldName, $operator, $value));
        return $qb;
    }

    /**
     * Adds a new And criterion
     * @param  string   $fieldName name of the field
     * @param  Operator $operator  operator
     * @param  mixed   $value     value
     * @return self              for chaining
     */
    public function and(string $fieldName, Operator $operator, $value): self
    {
        $this->addAnd(new Criterion($fieldName, $operator, $value));
        return $this;
    }

    /**
     * Adds a new Or criterion
     * @param  string   $fieldName name of the field
     * @param  Operator $operator  operator
     * @param  mixed   $value     value
     * @return self              for chaining
     */
    public function or(string $fieldName, Operator $operator, $value): self
    {
        $this->ors[] = new Criterion($fieldName, $operator, $value);
        return $this;
    }

    /**
     * Adds an AND criterion
     * @param Criterion $c criterion
     */

    private function addAnd(Criterion $c): void
    {
        $this->ands[] = $c;
    }

    /**
     * Adds an OR criterion
     * @param Criterion $c criterion
     */
    private function addOr(Criterion $c): void
    {
        $this->ors[] = $c;
    }

    /**
     * Builds the Query
     * @return Query
     */
    public function build(): QueryInterface
    {   
        return new Query($this->ands, $this->ors);
    }
}