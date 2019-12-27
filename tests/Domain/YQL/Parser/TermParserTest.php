<?php

namespace Tests\Morebec\YDB\Domain\YQL\Parser;


use Morebec\YDB\Domain\YQL\Parser\TermParser;
use Morebec\YDB\Domain\YQL\Parser\Lexer;
use PHPUnit\Framework\TestCase;

class TermParserTest extends TestCase
{

    public function testLexTerms()
    {
        $termLexer = new Lexer();
        $tokens = $termLexer->lex('/* a comment */ FIND ALL FROM table WHERE (field == 100) AND (field2 == false)');

        $termLexer = new TermParser();
        $termedTokens = $termLexer->lexTerms($tokens);

        $this->assertCount(17, $tokens);
        $this->assertCount(13, $termedTokens);
    }
}
