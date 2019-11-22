<?php 

use Morebec\YDB\Entity\Identity\RecordId;
use Morebec\YDB\Entity\Query\Term;
use Morebec\YDB\Entity\Query\Operator;
use Morebec\YDB\Entity\Record;
use Morebec\YDB\YQL\TermNode;
use Morebec\YDB\YQL\PYQL;
use Morebec\YDB\YQL\ExpressionNode;
use Morebec\YDB\YQL\ExpressionOperator;

/**
 * YQLTest
 */
class YQLTest extends \Codeception\Test\Unit
{
    public function testEvaluateSingleExpression()
    {
        $tree = new ExpressionNode(
            new TermNode(new Term('price', Operator::EQUAL(), 2.00))
        );

        $record = new Record(RecordId::generate(), [
            'price' => 2,
            'genre' => 'adventure'
        ]);

        $result = PYQL::evaluateForRecord($tree, $record);

        $this->assertTrue($result);
    }

    public function testEvaluateMultipleExpression()
    {
        // WHERE (price == 2) AND (genre == 'adventure')
        $tree = new ExpressionNode(
            new TermNode(new Term('price', Operator::EQUAL(), 2.00)), // Left
            new ExpressionOperator(ExpressionOperator::AND), // Operator
            new TermNode(new Term('genre', Operator::EQUAL(), 'adventure')) // Right
        );

        $record = new Record(RecordId::generate(), [
            'price' => 2,
            'genre' => 'adventure'
        ]);

        $result = PYQL::evaluateForRecord($tree, $record);

        $this->assertTrue($result);
    }

    public function testEvaluateDeepTree()
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
        $record = new Record(RecordId::generate(), [
            'price' => 5,
            'genre' => 'adventure'
        ]);
        $this->assertTrue(PYQL::evaluateForRecord($tree, $record));

        // Will NOT match
        $record = new Record(RecordId::generate(), [
            'price' => 5,
            'genre' => 'crime'
        ]);
        $this->assertFalse(PYQL::evaluateForRecord($tree, $record));
    }
}
