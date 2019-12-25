<?php


use Morebec\YDB\Domain\Model\Entity\Record;
use Morebec\YDB\Domain\Model\Entity\RecordId;
use Morebec\YDB\Domain\YQL\ExpressionNode;
use Morebec\YDB\Domain\YQL\ExpressionOperator;
use Morebec\YDB\Domain\YQL\PYQLEngine;
use Morebec\YDB\Domain\YQL\Query\Operator;
use Morebec\YDB\Domain\YQL\Query\Term;
use Morebec\YDB\Domain\YQL\TermNode;
use PHPUnit\Framework\TestCase;

/**
 * YQLTest
 */
class YQLEngineTest extends TestCase
{
    public function testEvaluateSingleExpression(): void
    {
        $tree = new ExpressionNode(
            new TermNode(new Term('price', Operator::EQUAL(), 2.00))
        );

        $record = Record::create(RecordId::generate(), [
            'price' => 2,
            'genre' => 'adventure'
        ]);

        $result = PYQLEngine::evaluateExpressionForRecord($tree, $record);

        $this->assertTrue($result);
    }

    public function testEvaluateMultipleExpression(): void
    {
        // WHERE (price == 2) AND (genre == 'adventure')
        $tree = new ExpressionNode(
            new TermNode(new Term('price', Operator::EQUAL(), 2.00)), // Left
            new ExpressionOperator(ExpressionOperator::AND), // Operator
            new TermNode(new Term('genre', Operator::EQUAL(), 'adventure')) // Right
        );

        $record = Record::create(RecordId::generate(), [
            'price' => 2,
            'genre' => 'adventure'
        ]);

        $result = PYQLEngine::evaluateExpressionForRecord($tree, $record);

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
            new TermNode(new Term('genre', Operator::EQUAL(), 'adventure')), 
            // Operator
            new ExpressionOperator(ExpressionOperator::AND),
            // Left
            new TermNode(new Term('price', Operator::EQUAL(), 5.00))
        );

        $exprB = new ExpressionNode(
            // Right
            new TermNode(new Term('genre', Operator::EQUAL(), 'crime')),
            // Operator
            new ExpressionOperator(ExpressionOperator::AND),
            // Left
            new TermNode(new Term('price', Operator::EQUAL(), 10.00))
        );

        $tree = new ExpressionNode(
            $exprA,
            new ExpressionOperator(ExpressionOperator::OR),
            $exprB
        );

        // Will match
        $record = Record::create(RecordId::generate(), [
            'price' => 5,
            'genre' => 'adventure'
        ]);
        $this->assertTrue(PYQLEngine::evaluateExpressionForRecord($tree, $record));

        // Will NOT match
        $record = Record::create(RecordId::generate(), [
            'price' => 5,
            'genre' => 'crime'
        ]);
        $this->assertFalse(PYQLEngine::evaluateExpressionForRecord($tree, $record));
    }
}
