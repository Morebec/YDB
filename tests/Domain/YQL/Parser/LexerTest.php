<?php

namespace Tests\Morebec\YDB\Domain\YQL\Parser;

use Morebec\YDB\Domain\YQL\Parser\Lexer;

use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{

    public function testParse(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->lex('FIND ALL FROM table WHERE field == 100');

        $this->assertNotEmpty($tokens);
    }

    public function testParseWithParens(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->lex('FIND ALL FROM table WHERE (field == 100) AND (field2 == false)');

        $this->assertNotEmpty($tokens);
    }


    public function testParseWithComments(): void
    {
        $lexer = new Lexer();
        $tokens = $lexer->lex('/* a comment */ FIND ALL FROM table WHERE (field == 100) AND (field2 == false)');

        $this->assertNotEmpty($tokens);
    }

    public function testParseWIthUnexpectedTokenThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $lexer = new Lexer();
        $tokens = $lexer->lex('FIND ALL FROM ! table WHERE field == 100');

    }
}
