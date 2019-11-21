<?php 

use Morebec\YDB\Entity\Identity\RecordId;
use Morebec\YDB\Entity\Query\Criterion;
use Morebec\YDB\Entity\Query\Operator;
use Morebec\YDB\Entity\Record;
use Morebec\YDB\YQL\CriterionNode;
use Morebec\YDB\YQL\PYQL;
use Morebec\YDB\YQL\TreeNode;
use Morebec\YDB\YQL\TreeOperator;

/**
 * YQLTest
 */
class YQLTest extends \Codeception\Test\Unit
{
    public function testEvaluateSingleExpression()
    {
        $tree = new TreeNode(
            new CriterionNode(new Criterion('price', Operator::EQUAL(), 2.00))
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
        $tree = new TreeNode(
            new CriterionNode(new Criterion('price', Operator::EQUAL(), 2.00)), // Left
            new TreeOperator(TreeOperator::AND), // Operator
            new CriterionNode(new Criterion('genre', Operator::EQUAL(), 'adventure')) // Right
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
        $exprA = new TreeNode(
            // Right
            new CriterionNode(new Criterion('genre', Operator::EQUAL(), 'adventure')), 
            // Operator
            new TreeOperator(TreeOperator::AND),
            // Left
            new CriterionNode(new Criterion('price', Operator::EQUAL(), 5.00))
        );

        $exprB = new TreeNode(
            // Right
            new CriterionNode(new Criterion('genre', Operator::EQUAL(), 'crime')),
            // Operator
            new TreeOperator(TreeOperator::AND),
            // Left
            new CriterionNode(new Criterion('price', Operator::EQUAL(), 10.00))
        );

        $tree = new TreeNode(
            $exprA,
            new TreeOperator(TreeOperator::OR),
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
