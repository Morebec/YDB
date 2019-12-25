<?php

use Morebec\YDB\legacy\Entity\Query\Operator;
use Morebec\YDB\QueryBuilder;
use PHPUnit\Framework\TestCase;

/**
 * QueryBuilderTest
 */
class QueryBuilderTest extends TestCase
{
    public function testBuild()
    {
        $query = QueryBuilder::where('price', Operator::EQUAL(), 5)
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
