<?php

namespace Morebec\YDB\YQL;

use Morebec\YDB\YQL\Query\ExpressionQuery;
use Morebec\YDB\YQL\Query\TermOperator;

class ExpressionQueryBuilder
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

    /**
     * ExpressionQueryBuilder constructor.
     * @param Cardinality $cardinality
     */
    public function __construct(Cardinality $cardinality)
    {
        $this->cardinality = $cardinality;
    }

    /**
     * @param Cardinality $cardinality
     * @return static
     */
    public static function find(Cardinality $cardinality): self
    {
        return new static($cardinality);
    }

    public function from(string $collectionName): void
    {
        $this->from = $collectionName;
    }

    /**
     * @param string $field
     * @param TermOperator $operator
     * @param mixed $value
     * @return $this
     */
    public function where(string $field, TermOperator $operator, $value): self
    {
        $this->rootNode = new TermNode($field, $operator, $value);
        return $this;
    }

    /**
     * @param string $field
     * @param TermOperator $operator
     * @param mixed $value
     * @return $this
     */
    public function addWhere(string $field, TermOperator $operator, $value): self
    {
        $where = new TermNode($field, $operator, $value);
        $this->insertNodeRight(ExpressionOperator::AND(), $where);

        return $this;
    }

    /**
     * @param string $field
     * @param TermOperator $operator
     * @param mixed $value
     * @return $this
     */
    public function orWhere(string $field, TermOperator $operator, $value): self
    {
        $where = new TermNode($field, $operator, $value);
        $this->insertNodeRight(ExpressionOperator::OR(), $where);

        return $this;
    }

    public function build(): ExpressionQuery
    {
        return new ExpressionQuery($this->cardinality, $this->from, $this->rootNode);
    }

    /**
     * @param ExpressionOperator $operator
     * @param TermNode $node
     */
    private function insertNodeRight(ExpressionOperator $operator, TermNode $node): void
    {
        $this->rootNode = new ExpressionNode($this->rootNode, $operator, $node);
    }
}
