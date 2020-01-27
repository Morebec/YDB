<?php

namespace Morebec\YDB\YQL;

use InvalidArgumentException;
use Morebec\ValueObjects\ValueObjectInterface;

/**
 * Represents a node in a AST Tree
 */
class ExpressionNode implements ValueObjectInterface
{
    /** @var ExpressionNode|null left */
    private $left;

    /** @var ExpressionNode|null right */
    private $right;

    /** @var ExpressionOperator operator */
    private $operator;

    public function __construct(
        ?ExpressionNode $left = null,
        ?ExpressionOperator $operator = null,
        ?ExpressionNode $right = null
    ) {
        if ($operator && !$right) {
            throw new InvalidArgumentException('An ExpressionNode cannot have an operator without a right node');
        }

        $this->left = $left;
        $this->operator = $operator;
        $this->right = $right;
    }

    /**
     * @return ExpressionNode
     */
    public function getLeft(): ?ExpressionNode
    {
        return $this->left;
    }

    /**
     * @return ExpressionNode|null
     */
    public function getRight(): ?ExpressionNode
    {
        return $this->right;
    }

    /**
     * @return ExpressionOperator
     */
    public function getOperator(): ?ExpressionOperator
    {
        return $this->operator;
    }

    /**
     * Indicates if this is a leaf
     * @return boolean true if leaf, otherwise false
     */
    public function isLeaf(): bool
    {
        return $this->left === null && $this->right === null;
    }

    public function isEqualTo(ValueObjectInterface $valueObject): bool
    {
        return (string)$this === (string)$valueObject;
    }

    /**
     * Returns a string representation of the value object
     *
     * @return string
     */
    public function __toString()
    {
        $str =  (string)$this->left;

        if ($this->operator) {
            $str = "({$str}) {$this->operator} ({$this->right})";
        }
        return $str;
    }
}
