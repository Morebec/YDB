<?php 

use Morebec\YDB\Entity\Query\Term;
use Morebec\YDB\Entity\Query\Operator;
use Morebec\YDB\YQL\TermNode;
use Morebec\YDB\YQL\ExpressionNode;
use Morebec\YDB\YQL\ExpressionOperator;

/**
 * ExpressionNodeTest
 */
class ExpressionNodeTest extends \Codeception\Test\Unit
{
    public function testStringRepresentation(): void
    {
        $tree = new ExpressionNode(
            new TermNode(new Term('price', Operator::EQUAL(), 2.00))
        );

        $this->assertEquals('(price == 2)', (string)$tree);

        // Left and Right
        $tree = new ExpressionNode(
            new TermNode(new Term('price', Operator::EQUAL(), 2.00)),
            new ExpressionOperator(ExpressionOperator::AND),
            new TermNode(new Term('genre', Operator::EQUAL(), 'adventure'))
        );

        $this->assertEquals("(price == 2) AND (genre == 'adventure')", (string)$tree);
    }    
}
