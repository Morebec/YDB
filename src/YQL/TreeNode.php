<?php 

namespace Morebec\YDB\YQL;

use Assert\Assertion;
use Morebec\ValueObjects\ValueObjectInterface;

/**
 * Represents a node in a AST Tree
 */
class TreeNode implements ValueObjectInterface
{
    /** @var TreeNode left */
    private $left;

    /** @var TreeNode|null right */
    private $right;

    /** @var TreeOperator operator */
    private $operator;

    function __construct(
        TreeNode $left, 
        ?TreeOperator $operator = null, 
        ?TreeNode $right = null
    )
    {
        if($operator) {
            Assertion::notNull(
                $right, 
                'A TreeNode cannot have an operator without a right node'
            );
        }

        $this->left = $left;
        $this->operator = $operator;
        $this->right = $right;
    }

    /**
     * @return TreeNode
     */
    public function getLeft(): TreeNode
    {
        return $this->left;
    }

    /**
     * @return TreeNode|null
     */
    public function getRight(): ?TreeNode
    {
        return $this->right;
    }

    /**
     * @return TreeOperator
     */
    public function getOperator(): ?TreeOperator
    {
        return $this->operator;
    }

    /**
     * Indicates if this value object is equal to abother value object
     * @param  ValueObjectInterface $valueObject othervalue object to compare to
     * @return boolean                           true if equal otherwise false
     */
    public function isEqualTo(ValueObjectInterface $valueObject): bool
    {
        throw new \Exception('Method isEqualTo() is not implemented.');
    }

    /**
     * Returns a string representation of the value object
     *
     * @return string
     */
    public function __toString()
    {
        $str = sprintf("(%s)", $this->left);

        if($this->operator && $this->right) {
            $str = $str . sprintf(" %s (%s)",
                $this->operator,
                $this->right
            );
        }

        return $str;
    }
}
