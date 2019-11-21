<?php 

namespace Morebec\YDB\Entity\Query;

use Morebec\ValueObjects\ValueObjectInterface;
use Morebec\YDB\Contract\QueryInterface;
use Morebec\YDB\Contract\RecordInterface;
use Morebec\YDB\YQL\TreeNode;

/**
 * Query
 */
class Query implements QueryInterface
{
    /** @var TreeNode expression */
    private $expression;

    function __construct(TreeNode $expression)
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
}