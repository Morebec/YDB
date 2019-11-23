<?php

namespace Morebec\YDB\YQL;

use Assert\Assertion;
use Morebec\ValueObjects\ValueObjectInterface;

/**
 * Represents a node in a AST Tree
 */
class ExpressionNode implements ValueObjectInterface
{
    /** @var ExpressionNode left */
    private $left;

    /** @var ExpressionNode|null right */
    private $right;

    /** @var ExpressionOperator operator */
    private $operator;

    public function __construct(
        ExpressionNode $left,
        ?ExpressionOperator $operator = null,
        ?ExpressionNode $right = null
    ) {
        if ($operator) {
            Assertion::notNull(
                $right,
                'A ExpressionNode cannot have an operator without a right node'
            );
        }

        $this->left = $left;
        $this->operator = $operator;
        $this->right = $right;
    }

    /**
     * @return ExpressionNode
     */
    public function getLeft(): ExpressionNode
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

        if ($this->operator && $this->right) {
            $str = $str . sprintf(
                " %s (%s)",
                $this->operator,
                $this->right
            );
        }

        return $str;
    }
}
