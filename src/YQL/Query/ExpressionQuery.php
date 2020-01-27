<?php

namespace Morebec\YDB\YQL\Query;

use Morebec\YDB\YQL\Cardinality;
use Morebec\YDB\YQL\ExpressionNode;

/**
 * Query
 */
class ExpressionQuery
{
    /** @var ExpressionNode expression */
    protected $expression;
    /**
     * @var Cardinality
     */
    protected $cardinality;
    /**
     * @var string
     */
    protected $collectionName;

    public function __construct(
        Cardinality $cardinality,
        string $collectionName,
        ExpressionNode $expression
    )
    {
        $this->expression = $expression;
        $this->cardinality = $cardinality;
        $this->collectionName = $collectionName;
    }

    /**
     * @param ExpressionQuery $query
     * @return bool
     */
    public function isEqualTo(ExpressionQuery $query): bool
    {
        return (string)$this === (string)$query;
    }

    /**
     * @return ExpressionNode
     */
    public function getExpression(): ExpressionNode
    {
        return $this->expression;
    }

    /**
     * @return Cardinality
     */
    public function getCardinality(): Cardinality
    {
        return $this->cardinality;
    }

    /**
     * @return string
     */
    public function getCollectionName(): string
    {
        return $this->collectionName;
    }

    public function __toString()
    {
        return (string)$this->expression;
    }
}
