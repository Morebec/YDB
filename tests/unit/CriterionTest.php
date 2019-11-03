<?php 

use Morebec\YDB\Criterion;
use Morebec\YDB\Operator;

/**
 * CriterionTest
 */
class CriterionTest extends \Codeception\Test\Unit
{
    public function testEqualWithString()
    {
        $c = new Criterion('test_field', Operator::EQUAL(), 'TestString');

        $this->assertTrue($c->valueMatches('TestString'));
        $this->assertFalse($c->valueMatches('TestString.not_equal'));
    }

    public function testStrictlyEqualWithString()
    {
        $c = new Criterion('test_field', Operator::STRICTLY_EQUAL(), 'TestString');

        $this->assertTrue($c->valueMatches('TestString'));
        $this->assertFalse($c->valueMatches('TestString.not_equal'));
    }

    public function testStrictlyNotEqualWithString()
    {
        $c = new Criterion('test_field', Operator::NOT_EQUAL(), 'TestString');

        $this->assertTrue($c->valueMatches('TestString.not_equal'));
        $this->assertFalse($c->valueMatches('TestString'));
    }

    public function testNotEqualWithString()
    {
        $c = new Criterion('test_field', Operator::STRICTLY_NOT_EQUAL(), 'TestString');

        $this->assertTrue($c->valueMatches('TestString.not_equal'));
        $this->assertFalse($c->valueMatches('TestString'));
    }

    public function testInWithString()
    {
        $c = new Criterion('test_field', Operator::IN(), ['string1', 'string2', 'TestString']);

        $this->assertTrue($c->valueMatches('TestString'));
        $this->assertFalse($c->valueMatches('TestString.not_in'));
    }

    public function testNotInWithString()
    {
        $c = new Criterion('test_field', Operator::NOT_IN(), ['string1', 'string2', 'TestString']);

        $this->assertFalse($c->valueMatches('TestString'));
        $this->assertTrue($c->valueMatches('TestString.not_in'));
    }


    // Integers
    public function testEqualWithInteger()
    {
        $c = new Criterion('test_field', Operator::EQUAL(), 42);

        $this->assertTrue($c->valueMatches(42));
        $this->assertFalse($c->valueMatches(99));
    }

    public function testStrictlyEqualWithInteger()
    {
        $c = new Criterion('test_field', Operator::STRICTLY_EQUAL(), 42);

        $this->assertTrue($c->valueMatches(42));
        $this->assertFalse($c->valueMatches(99));
    }

    public function testStrictlyNotEqualWithInteger()
    {
        $c = new Criterion('test_field', Operator::NOT_EQUAL(), 42);

        $this->assertTrue($c->valueMatches(99));
        $this->assertFalse($c->valueMatches(42));
    }

    public function testNotEqualWithInteger()
    {
        $c = new Criterion('test_field', Operator::STRICTLY_NOT_EQUAL(), 42);

        $this->assertTrue($c->valueMatches(99));
        $this->assertFalse($c->valueMatches(42));
    }

    public function testLessWithInteger()
    {
        $c = new Criterion('test_field', Operator::LESS_THAN(), 42);

        $this->assertTrue($c->valueMatches(25));
    }

    public function testLessOrEqualWithInteger()
    {
        $c = new Criterion('test_field', Operator::LESS_OR_EQUAL(), 42);

        $this->assertTrue($c->valueMatches(42));
        $this->assertFalse($c->valueMatches(44));
    }

    public function testGreaterWithInteger()
    {
        $c = new Criterion('test_field', Operator::GREATER_THAN(), 42);

        $this->assertTrue($c->valueMatches(78));
        $this->assertFalse($c->valueMatches(42));
        $this->assertFalse($c->valueMatches(24));
    }

    public function testGreaterOrEqualWithInteger()
    {
        $c = new Criterion('test_field', Operator::GREATER_OR_EQUAL(), 42);

        $this->assertTrue($c->valueMatches(42));
        $this->assertTrue($c->valueMatches(48));
        $this->assertFalse($c->valueMatches(24));
    }

    public function testInWithInteger()
    {
        $c = new Criterion('test_field', Operator::IN(), [1, 2, 42]);

        $this->assertTrue($c->valueMatches(42));
        $this->assertFalse($c->valueMatches(0));
    }

    public function testNotInWithInteger()
    {
        $c = new Criterion('test_field', Operator::NOT_IN(), [1, 2, 42]);

        $this->assertFalse($c->valueMatches(42));
        $this->assertTrue($c->valueMatches(0));
    }


    // Float
    public function testEqualWithFloat()
    {
        $c = new Criterion('test_field', Operator::EQUAL(), 42.5);

        $this->assertTrue($c->valueMatches(42.5));
        $this->assertFalse($c->valueMatches(99));
    }

    public function testStrictlyEqualWithFloat()
    {
        $c = new Criterion('test_field', Operator::STRICTLY_EQUAL(), 42.5);

        $this->assertTrue($c->valueMatches(42.5));
        $this->assertFalse($c->valueMatches(42));
        $this->assertFalse($c->valueMatches(99));
    }

    public function testStrictlyNotEqualWithFloat()
    {
        $c = new Criterion('test_field', Operator::NOT_EQUAL(), 42.5);

        $this->assertTrue($c->valueMatches(99));
        $this->assertFalse($c->valueMatches(42.5));
    }

    public function testNotEqualWithFloat()
    {
        $c = new Criterion('test_field', Operator::STRICTLY_NOT_EQUAL(), 42.5);

        $this->assertTrue($c->valueMatches(99));
        $this->assertFalse($c->valueMatches(42.5));
    }

    public function testLessWithFloat()
    {
        $c = new Criterion('test_field', Operator::LESS_THAN(), 42.5);

        $this->assertTrue($c->valueMatches(25));
        $this->assertFalse($c->valueMatches(42.6));
    }

    public function testLessOrEqualWithFloat()
    {
        $c = new Criterion('test_field', Operator::LESS_OR_EQUAL(), 42.5);

        $this->assertTrue($c->valueMatches(42.5));
        $this->assertFalse($c->valueMatches(42.6));
        $this->assertFalse($c->valueMatches(44));
    }

    public function testGreaterWithFloat()
    {
        $c = new Criterion('test_field', Operator::GREATER_THAN(), 42.5);

        $this->assertTrue($c->valueMatches(78));
        $this->assertFalse($c->valueMatches(42.4));
        $this->assertFalse($c->valueMatches(42.5));
        $this->assertFalse($c->valueMatches(24.6));
    }

    public function testGreaterOrEqualWithFloat()
    {
        $c = new Criterion('test_field', Operator::GREATER_OR_EQUAL(), 42.5);

        $this->assertTrue($c->valueMatches(42.5));
        $this->assertTrue($c->valueMatches(48));
        $this->assertFalse($c->valueMatches(24));
    }

    public function testInWithFloat()
    {
        $c = new Criterion('test_field', Operator::IN(), [1, 2, 42.5]);

        $this->assertTrue($c->valueMatches(42.5));
        $this->assertFalse($c->valueMatches(0.5));
    }

    public function testNotInWithFloat()
    {
        $c = new Criterion('test_field', Operator::NOT_IN(), [1, 2, 42.5]);

        $this->assertFalse($c->valueMatches(42.5));
        $this->assertTrue($c->valueMatches(0.5));
    }

    // Boolean
    public function testEqualWithBoolean()
    {
        $c = new Criterion('test_field', Operator::EQUAL(), true);

        $this->assertTrue($c->valueMatches(true));
        $this->assertFalse($c->valueMatches(false));
    }

    public function testStrictlyEqualWithBoolean()
    {
        $c = new Criterion('test_field', Operator::EQUAL(), true);

        $this->assertTrue($c->valueMatches(true));
        $this->assertFalse($c->valueMatches(false));
    }

    public function testNotEqualWithBoolean()
    {
        $c = new Criterion('test_field', Operator::NOT_EQUAL(), true);

        $this->assertFalse($c->valueMatches(true));
        $this->assertTrue($c->valueMatches(false));
    }

    public function testStrictlyNotEqualWithBoolean()
    {
        $c = new Criterion('test_field', Operator::STRICTLY_NOT_EQUAL(), true);

        $this->assertFalse($c->valueMatches(true));
        $this->assertTrue($c->valueMatches(false));
    }
}