<?php 

use Morebec\YDB\YQL\YQL;

/**
 * YQLTest tests add on functionsi n YQL
 */
class YQLTest extends \Codeception\Test\Unit
{
    public function testStringExpressionProvider()
    {
        $lang = new YQL();

        // Lowercase
        $ret = $lang->evaluate('lowercase(firstname) == "jane"', [
            'firstname' => 'Jane',
            'lastname' => 'Doe'
        ]);

        $this->assertTrue($ret);

        // Uppercase
        $ret = $lang->evaluate('uppercase(firstname) == "JANE"', [
            'firstname' => 'Jane',
            'lastname' => 'Doe'
        ]);

        $this->assertTrue($ret);


        // Trim
        $ret = $lang->evaluate('firstname == trim(" Jane ")', [
            'firstname' => 'Jane',
            'lastname' => 'Doe'
        ]);

        $this->assertTrue($ret);
    }
}