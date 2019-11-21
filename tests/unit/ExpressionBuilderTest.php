<?php 

use Morebec\YDB\Entity\Query\Operator;
use Morebec\YDB\ExpressionBuilder;

/**
 * ExpressionBuilderTest
 */
class ExpressionBuilderTest extends \Codeception\Test\Unit
{
    public function testBuild()
    {
        $exp = ExpressionBuilder::where('price', Operator::EQUAL(), 5)
                                ->andWhere('genre', Operator::EQUAL(), 'adventure')
                                ->orWhere('price', Operator::EQUAL(), 2)
                                ->build()
        ;

        $this->assertEquals(
            "((price == 5) AND (genre == 'adventure')) OR (price == 2)", 
            (string)$exp
        );
    }
}
