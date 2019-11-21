<?php 

namespace Morebec\YDB;

use Morebec\YDB\Entity\Query\Criterion;
use Morebec\YDB\Entity\Query\Operator;
use Morebec\YDB\YQL\CriterionNode;
use Morebec\YDB\YQL\TreeNode;
use Morebec\YDB\YQL\TreeOperator;

/**
 * ExpressionBuilder
 */
class ExpressionBuilder
{
    /** @var TreeNode root tree node */
    protected $root;

    private function __construct(TreeNode $root)
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
    public static function where(string $fieldName, Operator $operator, $value): self
    {
        $whereNode = new CriterionNode(new Criterion($fieldName, $operator, $value));
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
        $whereNode = new CriterionNode(new Criterion($fieldName, $operator, $value));
        $this->addNodeRight(new TreeOperator(TreeOperator::AND), $whereNode);
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
        $whereNode = new CriterionNode(new Criterion($fieldName, $operator, $value));
        $this->addNodeRight(new TreeOperator(TreeOperator::OR), $whereNode);
        return $this;
    }

    /**
     * Builds the Expression and returns it
     * @return TreeNode
     */
    public function build()
    {
        return $this->root;
    }

    /**
     * Adds a node to the right of the root
     * @param TreeOpeartor $operator operator
     * @param TreeNode     $node     node
     */
    private function addNodeRight(TreeOperator $operator, TreeNode $node): void
    {
        if(!$this->root) {
            throw new \Exception("An expression must start with a simple where clause");
        }

        $this->root = new TreeNode($this->root, $operator, $node);
    }
}