<?php

namespace Morebec\YDB\Domain\YQL\Query;

use Morebec\ValueObjects\ValueObjectInterface;
use Morebec\YDB\Domain\Model\Entity\Record;
use Morebec\YDB\Domain\YQL\ExpressionNode;

/**
 * Query
 */
class Query
{
    /** @var ExpressionNode expression */
    private $expression;

    public function __construct(ExpressionNode $expression)
    {
        $this->expression = $expression;
    }

    /**
     * Indicates if a record matches this query
     * @param Record $record
     * @return bool             true if it matches otherwise false
     */
    public function matchesRecord(Record $record): bool
    {
    }

    public function isEqualTo(ValueObjectInterface $vo): bool
    {
        return (string)$this === (string)$vo;
    }

    public function __toString()
    {
        return (string)$this->expression;
    }

    public function getExpressionNode(): ExpressionNode
    {
        return $this->expression;
    }
}
