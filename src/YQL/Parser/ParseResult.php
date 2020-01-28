<?php


namespace Morebec\YDB\YQL\Parser;

use Morebec\YDB\YQL\Cardinality;
use Morebec\YDB\YQL\ExpressionNode;

class ParseResult
{
    /** @var Cardinality */
    public $cardinality;

    /** @var string */
    public $collectionName;

    /** @var ExpressionNode */
    public $expressionNode;

    public function __construct(Cardinality $cardinality, string $collectionName, ExpressionNode $expressionNode)
    {
        $this->cardinality = $cardinality;
        $this->collectionName = $collectionName;
        $this->expressionNode = $expressionNode;
    }
}
