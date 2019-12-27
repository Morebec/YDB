<?php


namespace Morebec\YDB\Domain\YQL;


use Morebec\YDB\Domain\YQL\Query\Operator;
use Morebec\YDB\Domain\YQL\Query\Term;

class QueryBuilder2
{

    /**
     * @var Cardinality
     */
    private $cardinality;

    /**
     * @var string
     */
    private $from;

    /**
     * @var ExpressionNode
     */
    private $rootNode;

    public function __construct(Cardinality $cardinality)
    {
        $this->cardinality = $cardinality;
    }

    public static function find(Cardinality $cardinality): self
    {
        return new static($cardinality);
    }

    public function from(string $table) {
        $this->from = $table;
    }

    public function where(string $column, Operator $operator, $value): self
    {
        $this->rootNode = new TermNode(new Term($column, $operator, $value));
    }

    public function addWhere(string $column, Operator $operator, $value): self
    {

    }

    public function orWhere(string $column, Operator $operator, $value): self
    {

    }
}