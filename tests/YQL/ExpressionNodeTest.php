<?php


use Morebec\YDB\YQL\ExpressionNode;
use Morebec\YDB\YQL\ExpressionOperator;
use Morebec\YDB\YQL\Query\TermOperator;
use Morebec\YDB\YQL\TermNode;
use PHPUnit\Framework\TestCase;

/**
 * ExpressionNodeTest
 */
class ExpressionNodeTest extends TestCase
{
    public function testStringRepresentation(): void
    {
        $tree = new ExpressionNode(
            new TermNode('price', TermOperator::EQUAL(), 2.00)
        );

        $this->assertEquals('price === 2', (string)$tree);

        // Left and Right
        $tree = new ExpressionNode(
            new TermNode('price', TermOperator::EQUAL(), 2.00),
            ExpressionOperator::AND(),
            new TermNode('genre', TermOperator::EQUAL(), 'adventure')
        );

        $this->assertEquals('(price === 2) AND (genre === "adventure")', (string)$tree);
    }
}
