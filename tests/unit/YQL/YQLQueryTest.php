<?php 

use Morebec\YDB\YQL\YQLQuery;
use Morebec\YDB\Record;
use Morebec\YDB\RecordId;
use Symfony\Component\ExpressionLanguage\SyntaxError;

/**
 * YQLQueryTest
 */
class YQLQueryTest extends Codeception\Test\Unit
{
    public function testOneFieldQuery()
    {
        $record = new Record(RecordId::generate(), [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'age' => 34,
            'email' => 'john.doe@email.com'
        ]);

        $query = new YQLQuery('firstname == "John"');

        $this->assertTrue($query->matches($record));

        $query = new YQLQuery('firstname == "Jane"');
        $this->assertFalse($query->matches($record));
    }

    public function testMultipleFieldQuery()
    {
        $record = new Record(RecordId::generate(), [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'age' => 34,
            'email' => 'john.doe@email.com'
        ]);

        $query = new YQLQuery('firstname == "John" && lastname == "Doe"');

        $this->assertTrue($query->matches($record));
    }

    public function testComputationQuery()
    {
        $record = new Record(RecordId::generate(), [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'age' => 34,
            'email' => 'john.doe@email.com'
        ]);

        $query = new YQLQuery('firstname ~ " " ~ lastname == "John Doe"');

        $this->assertTrue($query->matches($record));
    }

    public function testEmptyQueryThrowsException()
    {
        $record = new Record(RecordId::generate(), [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'age' => 34,
            'email' => 'john.doe@email.com'
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $query = new YQLQuery('');
    }

    public function testInvalidQueryThrowsException()
    {
        $record = new Record(RecordId::generate(), [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'age' => 34,
            'email' => 'john.doe@email.com'
        ]);

        $this->expectException(SyntaxError::class);
        $query = new YQLQuery('a ^= . o % 6i \k');
        $query->matches($record);
    }
}