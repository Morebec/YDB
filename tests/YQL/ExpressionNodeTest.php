<?php


use Morebec\YDB\Domain\YQL\ExpressionNode;
use Morebec\YDB\Domain\YQL\ExpressionOperator;
use Morebec\YDB\Domain\YQL\Query\Term;
use Morebec\YDB\Domain\YQL\Query\TermOperator;
use Morebec\YDB\Domain\YQL\TermNode;
use PHPUnit\Framework\TestCase;

/**
 * ExpressionNodeTest
 */
class ExpressionNodeTest extends TestCase
{
    public function testStringRepresentation(): void
    {
        $tree = new ExpressionNode(
            new TermNode(new Term('price', TermOperator::EQUAL(), 2.00))
        );

        $this->assertEquals('(price == 2)', (string)$tree);

        // Left and Right
        $tree = new ExpressionNode(
            new TermNode(new Term('price', TermOperator::EQUAL(), 2.00)),
            new ExpressionOperator(ExpressionOperator::AND),
            new TermNode(new Term('genre', TermOperator::EQUAL(), 'adventure'))
        );

        $this->assertEquals("(price == 2) AND (genre == 'adventure')", (string)$tree);
    }    
}
