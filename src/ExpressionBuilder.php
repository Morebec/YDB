<?php

namespace Morebec\YDB;

use Morebec\YDB\Entity\Query\Term;
use Morebec\YDB\Entity\Query\Operator;
use Morebec\YDB\YQL\TermNode;
use Morebec\YDB\YQL\ExpressionNode;
use Morebec\YDB\YQL\ExpressionOperator;

/**
 * ExpressionBuilder
 */
class ExpressionBuilder
{
    /** @var ExpressionNode root tree node */
    protected $root;

    private function __construct(ExpressionNode $root)
    {
        $this->root = $root;
    }

    /**
     * Add a where clause in the expression
     * @param  string   $fieldName name of the field
     * @param  Operator $operator  operator
     * @param  mixed   $value      value
     * @return self                for chaining
     */
    public static function where(string $fieldName, Operator $operator, $value): ExpressionBuilder
    {
        $whereNode = new TermNode(new Term($fieldName, $operator, $value));
        return new static($whereNode);
    }

    /**
     * Adds an AND where clause to the expression
     * @param  string   $fieldName name of the field
     * @param  Operator $operator  operator
     * @param  mixed   $value      value
     * @return self                for chaining
     */
    public function andWhere(string $fieldName, Operator $operator, $value): self
    {
        $whereNode = new TermNode(new Term($fieldName, $operator, $value));
        $this->insertNodeRight(new ExpressionOperator(ExpressionOperator::AND), $whereNode);
        return $this;
    }

    /**
     * Adds a OR clause to the exprnewession
     * Adds an AND where clause to the expression
     * @param  string   $fieldName name of the field
     * @param  Operator $operator  operator
     * @param  mixed   $value      value
     * @return self                for chaining
     */
    public function orWhere(string $fieldName, Operator $operator, $value): self
    {
        $whereNode = new TermNode(new Term($fieldName, $operator, $value));
        $this->insertNodeRight(new ExpressionOperator(ExpressionOperator::OR), $whereNode);
        return $this;
    }

    /**
     * Builds the Expression and returns it
     * @return ExpressionNode
     */
    public function build()
    {
        return $this->root;
    }

    /**
     * Adds a node to the right of the root
     * @param TreeOpeartor $operator operator
     * @param ExpressionNode     $node     node
     */
    private function insertNodeRight(ExpressionOperator $operator, ExpressionNode $node): void
    {
        if (!$this->root) {
            throw new \Exception("An expression must start with a simple where clause");
        }

        $this->root = new ExpressionNode($this->root, $operator, $node);
    }
}
