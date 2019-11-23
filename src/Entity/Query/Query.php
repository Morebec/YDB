<?php

namespace Morebec\YDB\Entity\Query;

use Morebec\ValueObjects\ValueObjectInterface;
use Morebec\YDB\Contract\QueryInterface;
use Morebec\YDB\Contract\RecordInterface;
use Morebec\YDB\YQL\ExpressionNode;

/**
 * Query
 */
class Query implements QueryInterface
{
    /** @var ExpressionNode expression */
    private $expression;

    public function __construct(ExpressionNode $expression)
    {
        $this->expression = $expression;
    }

    /**
     * Indicates if a record matches this query
     * @param  RecordInterface $r query
     * @return bool             true if it matches otherwise false
     */
    public function matchesRecord(RecordInterface $record): bool
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

    public function getExpression(): ExpressionNode
    {
        return $this->expression;
    }
}
