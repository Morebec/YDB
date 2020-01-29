<?php

namespace YQL\Parser;

use Morebec\YDB\YQL\Parser\Exception\UnexpectedTokenException;
use Morebec\YDB\YQL\Parser\Lexer;
use Morebec\YDB\YQL\Parser\TermParser;
use PHPUnit\Framework\TestCase;

class TermParserTest extends TestCase
{
    /**
     * @throws UnexpectedTokenException
     */
    public function testLexTermsMissingWhereClauseTermThrowsException(): void
    {
        $query = 'FIND ALL FROM collection WHERE';
        $this->assertInvalidQuery($query);
    }

    /**
     * @throws UnexpectedTokenException
     */
    public function testLexTermsMissingCollectionThrowsException(): void
    {
        $query = 'FIND ALL FROM';
        $this->assertInvalidQuery($query);
    }

    /**
     * @throws UnexpectedTokenException
     */
    public function testLexTermsMissingAndClauseThrowsException(): void
    {
        $query = 'FIND ALL FROM collection WHERE a === 2 AND';
        $this->assertInvalidQuery($query);
    }

    /**
     * @throws UnexpectedTokenException
     */
    public function testLexTermsMissingOrClauseThrowsException(): void
    {
        $query = 'FIND ALL FROM collection WHERE a === 2 OR';
        $this->assertInvalidQuery($query);
    }

    /**
     * @throws UnexpectedTokenException
     */
    public function testLexTermsWrongExpressionOperatorThrowsException(): void
    {
        $query = 'FIND ALL FROM collection WHERE a === 2 BUT o === 5';
        $this->assertInvalidQuery($query);
    }

    /**
     * @param string $query
     * @throws UnexpectedTokenException
     */
    private function assertInvalidQuery(string $query): void
    {
        $lexer = new Lexer();
        $termParser = new TermParser();
        $tokens = $lexer->lex($query);
        $this->expectException(UnexpectedTokenException::class);
        $termParser->parseTerms($tokens);
    }
}
