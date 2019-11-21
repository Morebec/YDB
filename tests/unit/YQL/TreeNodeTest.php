<?php 

use Morebec\YDB\Entity\Query\Criterion;
use Morebec\YDB\Entity\Query\Operator;
use Morebec\YDB\YQL\CriterionNode;
use Morebec\YDB\YQL\TreeNode;
use Morebec\YDB\YQL\TreeOperator;

/**
 * TreeNodeTest
 */
class TreeNodeTest extends \Codeception\Test\Unit
{
    public function testStringRepresentation(): void
    {
        $tree = new TreeNode(
            new CriterionNode(new Criterion('price', Operator::EQUAL(), 2.00))
        );

        $this->assertEquals('(price == 2)', (string)$tree);

        // Left and Right
        $tree = new TreeNode(
            new CriterionNode(new Criterion('price', Operator::EQUAL(), 2.00)),
            new TreeOperator(TreeOperator::AND),
            new CriterionNode(new Criterion('genre', Operator::EQUAL(), 'adventure'))
        );

        $this->assertEquals("(price == 2) AND (genre == 'adventure')", (string)$tree);
    }    
}
