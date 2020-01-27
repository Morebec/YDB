<?php

use Morebec\YDB\ExpressionQueryBuilder;
use Morebec\YDB\legacy\Entity\Query\Operator;
use PHPUnit\Framework\TestCase;

/**
 * QueryBuilderTest
 */
class QueryBuilderTest extends TestCase
{
    public function testBuild()
    {
        $query = ExpressionQueryBuilder::where('price', Operator::EQUAL(), 5)
                                ->andWhere('genre', Operator::EQUAL(), 'adventure')
                                ->orWhere('price', Operator::EQUAL(), 2)
                                ->build()
        ;

        $this->assertEquals(
            "((price == 5) AND (genre == 'adventure')) OR (price == 2)", 
            (string)$query
        );
    }    
}
