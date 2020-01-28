<?php


use Morebec\YDB\Document;
use Morebec\YDB\YQL\ExpressionNode;
use Morebec\YDB\YQL\ExpressionOperator;
use Morebec\YDB\YQL\PYQLQueryEvaluator;
use Morebec\YDB\YQL\Query\TermOperator;
use Morebec\YDB\YQL\TermNode;
use PHPUnit\Framework\TestCase;

/**
 * YQLTest
 */
class YQLEngineTest extends TestCase
{
    public function testEvaluateSingleExpression(): void
    {
        $tree = new ExpressionNode(
            new TermNode('price', TermOperator::EQUAL(), 2)
        );

        $record = Document::create([
            'price' => 2,
            'genre' => 'adventure'
        ]);

        $result = PYQLQueryEvaluator::evaluateExpressionForDocument($tree, $record);

        $this->assertTrue($result);
    }

    public function testEvaluateMultipleExpression(): void
    {
        // WHERE (price == 2) AND (genre == 'adventure')
        $tree = new ExpressionNode(
            new TermNode('price', TermOperator::EQUAL(), 2.00), // Left
            ExpressionOperator::AND(), // TermOperator
            new TermNode('genre', TermOperator::EQUAL(), 'adventure') // Right
        );

        $record = Document::create([
            'price' => 2.00,
            'genre' => 'adventure'
        ]);

        $result = PYQLQueryEvaluator::evaluateExpressionForDocument($tree, $record);

        $this->assertTrue($result);
    }

    public function testEvaluateDeepTree(): void
    {
        # Find find 5$ adventure books, or 10$ crime books
        # FIND 
        #        WHERE (genre == 'adventure' AND price == '5.00') // exprA
        #    OR 
        #        WHERE (genre == 'crime' AND price == '10.00') // exprB
        $exprA = new ExpressionNode(
            // Right
            new TermNode('genre', TermOperator::EQUAL(), 'adventure'),
            // TermOperator
            new ExpressionOperator(ExpressionOperator::AND),
            // Left
            new TermNode('price', TermOperator::EQUAL(), 5.00)
        );

        $exprB = new ExpressionNode(
            // Right
            new TermNode('genre', TermOperator::EQUAL(), 'crime'),
            // TermOperator
            new ExpressionOperator(ExpressionOperator::AND),
            // Left
            new TermNode('price', TermOperator::EQUAL(), 10.00)
        );

        $tree = new ExpressionNode(
            $exprA,
            new ExpressionOperator(ExpressionOperator::OR),
            $exprB
        );

        // Will match
        $record = Document::create([
            'price' => 5.00,
            'genre' => 'adventure'
        ]);
        $this->assertTrue(PYQLQueryEvaluator::evaluateExpressionForDocument($tree, $record));

        // Will NOT match
        $record = Document::create([
            'price' => 5.00,
            'genre' => 'crime'
        ]);
        $this->assertFalse(PYQLQueryEvaluator::evaluateExpressionForDocument($tree, $record));
    }
}
